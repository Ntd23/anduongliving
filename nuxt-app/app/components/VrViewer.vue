<template>
  <div class="vr-fullscreen">
    <div ref="viewerContainer" class="vr-canvas">
      <canvas ref="canvasRef" class="vr-canvas-el"></canvas>

      <div class="vr-hotspots">
        <div
          v-for="hotspot in visibleHotspots"
          :key="hotspot.id"
          class="vr-hotspot"
          :style="hotspot.screenStyle"
          @click="handleRoomChange(hotspot)"
        >
          <div class="vr-hotspot__arrow">></div>
          <div class="vr-hotspot__label">
            {{ hotspot.name }}
          </div>
        </div>
      </div>

      <div class="vr-controls">
        <button class="vr-btn" @click="zoomIn">+</button>
        <button class="vr-btn" @click="zoomOut">-</button>
        <button class="vr-btn" @click="lookUp">^</button>
        <button class="vr-btn" @click="lookDown">v</button>
        <button class="vr-btn" @click="lookLeft">&lt;</button>
        <button class="vr-btn" @click="lookRight">&gt;</button>
        <button class="vr-btn" @click="resetView">o</button>
      </div>
    </div>

    <div v-if="isLoading" class="vr-loading">
      Loading...
    </div>

    <div v-if="hasError" class="vr-error">
      {{ errorMessage }}
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watch, nextTick } from 'vue'
import * as THREE from 'three'

const props = defineProps({
  src: { type: String, required: true },
  scenes: {
    type: Array,
    default: () => []
  },
  rooms: {
    type: Array,
    default: () => []
  }
})

const viewerContainer = ref(null)
const canvasRef = ref(null)

const isLoading = ref(true)
const hasError = ref(false)
const errorMessage = ref('')

let scene = null
let camera = null
let renderer = null
let sphere = null
let animationId = null

let isDragging = false
let prevX = 0
let prevY = 0

const cameraRotation = ref({
  x: 0,
  y: 0
})

const renderTick = ref(0)

const normalizedScenes = computed(() => {
  if (Array.isArray(props.scenes) && props.scenes.length > 0) {
    return props.scenes
      .map((sceneItem, sceneIndex) => normalizeScene(sceneItem, sceneIndex))
      .filter(Boolean)
  }

  return normalizeLegacyRooms(props.rooms)
})

const currentScene = computed(() => {
  return normalizedScenes.value.find((sceneItem) => sceneItem.image === props.src) || null
})

const visibleHotspots = computed(() => {
  renderTick.value

  if (!camera || !currentScene.value) return []

  return currentScene.value.hotspots
    .map((hotspot) => {
      const style = projectHotspot(hotspot)
      return {
        ...hotspot,
        screenStyle: style
      }
    })
    .filter((hotspot) => hotspot.screenStyle.display !== 'none')
})

function normalizeScene(sceneItem, sceneIndex = 0) {
  if (!sceneItem || !sceneItem.image) {
    return null
  }

  const hotspots = Array.isArray(sceneItem.hotspots)
    ? sceneItem.hotspots
        .map((hotspot, hotspotIndex) => {
          const targetImage = hotspot?.targetImage || hotspot?.image

          if (!targetImage || targetImage === sceneItem.image) {
            return null
          }

          return {
            id: hotspot?.id ?? `scene-${sceneIndex + 1}-hotspot-${hotspotIndex + 1}`,
            name: hotspot?.name || `Gian ${hotspotIndex + 1}`,
            targetImage,
            yaw: Number(hotspot?.yaw ?? 0),
            pitch: Number(hotspot?.pitch ?? -10)
          }
        })
        .filter(Boolean)
    : []

  return {
    id: sceneItem.id ?? `scene-${sceneIndex + 1}`,
    name: sceneItem.name || `Gian ${sceneIndex + 1}`,
    image: sceneItem.image,
    hotspots
  }
}

function normalizeLegacyRooms(rooms) {
  if (!Array.isArray(rooms)) {
    return []
  }

  const normalizedRooms = rooms
    .map((room, roomIndex) => {
      if (!room?.image) {
        return null
      }

      return {
        id: room.id ?? `legacy-room-${roomIndex + 1}`,
        name: room.name || `Gian ${roomIndex + 1}`,
        image: room.image,
        yaw: Number(room.yaw ?? 0),
        pitch: Number(room.pitch ?? -10)
      }
    })
    .filter(Boolean)

  return normalizedRooms.map((room) => ({
    id: room.id,
    name: room.name,
    image: room.image,
    hotspots: normalizedRooms
      .filter((targetRoom) => targetRoom.image !== room.image)
      .map((targetRoom) => ({
        id: `${room.id}-to-${targetRoom.id}`,
        name: targetRoom.name,
        targetImage: targetRoom.image,
        yaw: targetRoom.yaw,
        pitch: targetRoom.pitch
      }))
  }))
}

function projectHotspot(hotspot) {
  if (!camera || !viewerContainer.value) {
    return { display: 'none' }
  }

  let yaw = Number(hotspot.yaw ?? 0)
  let pitch = Number(hotspot.pitch ?? -10)

  yaw = normalizeYaw(yaw)

  const phi = THREE.MathUtils.degToRad(90 - pitch)
  const theta = THREE.MathUtils.degToRad(180 - yaw)

  const radius = 500
  const x = radius * Math.sin(phi) * Math.cos(theta)
  const y = radius * Math.cos(phi)
  const z = radius * Math.sin(phi) * Math.sin(theta)

  const vector = new THREE.Vector3(x, y, z)
  const cameraDirection = new THREE.Vector3()

  camera.getWorldDirection(cameraDirection)

  if (cameraDirection.dot(vector.clone().normalize()) <= 0) {
    return { display: 'none' }
  }

  vector.project(camera)

  if (vector.z > 1 || vector.z < -1) {
    return { display: 'none' }
  }

  const width = viewerContainer.value.clientWidth
  const height = viewerContainer.value.clientHeight
  const left = (vector.x * 0.5 + 0.5) * width
  const top = (-vector.y * 0.5 + 0.5) * height

  if (left < -80 || left > width + 80 || top < -80 || top > height + 80) {
    return { display: 'none' }
  }

  const rotation = yaw - THREE.MathUtils.radToDeg(camera.rotation.y)

  return {
    left: `${left}px`,
    top: `${top}px`,
    '--hotspot-rotation': `${rotation}deg`
  }
}

async function initViewer() {
  destroyViewer()

  try {
    isLoading.value = true
    hasError.value = false

    const canvas = canvasRef.value

    scene = new THREE.Scene()

    camera = new THREE.PerspectiveCamera(
      75,
      canvas.clientWidth / canvas.clientHeight,
      0.1,
      1100
    )

    camera.position.set(0, 0, 0.1)

    renderer = new THREE.WebGLRenderer({
      canvas,
      antialias: true
    })

    renderer.setSize(canvas.clientWidth, canvas.clientHeight)
    renderer.setPixelRatio(window.devicePixelRatio)

    const geometry = new THREE.SphereGeometry(500, 60, 40)
    geometry.scale(-1, 1, 1)

    const texture = await new THREE.TextureLoader().loadAsync(props.src)
    const material = new THREE.MeshBasicMaterial({
      map: texture
    })

    sphere = new THREE.Mesh(geometry, material)
    scene.add(sphere)

    bindEvents()
    animate()

    isLoading.value = false
  } catch (error) {
    hasError.value = true
    errorMessage.value = 'Khong the load anh 360'
    isLoading.value = false
  }
}

function animate() {
  animationId = requestAnimationFrame(animate)

  camera.rotation.order = 'YXZ'
  camera.rotation.x = cameraRotation.value.x
  camera.rotation.y = cameraRotation.value.y

  renderer.render(scene, camera)

  renderTick.value++
}

function normalizeYaw(yaw) {
  while (yaw > 180) yaw -= 360
  while (yaw < -180) yaw += 360
  return yaw
}

function bindEvents() {
  const canvas = canvasRef.value

  canvas.addEventListener('mousedown', onMouseDown)
  canvas.addEventListener('mousemove', onMouseMove)
  canvas.addEventListener('mouseup', onMouseUp)
  canvas.addEventListener('mouseleave', onMouseUp)
  canvas.addEventListener('wheel', onWheel, { passive: false })

  window.addEventListener('resize', onResize)
}

function unbindEvents() {
  const canvas = canvasRef.value
  if (!canvas) return

  canvas.removeEventListener('mousedown', onMouseDown)
  canvas.removeEventListener('mousemove', onMouseMove)
  canvas.removeEventListener('mouseup', onMouseUp)
  canvas.removeEventListener('mouseleave', onMouseUp)
  canvas.removeEventListener('wheel', onWheel)

  window.removeEventListener('resize', onResize)
}

function onMouseDown(e) {
  isDragging = true
  prevX = e.clientX
  prevY = e.clientY
}

function onMouseMove(e) {
  if (!isDragging) return

  const dx = e.clientX - prevX
  const dy = e.clientY - prevY

  prevX = e.clientX
  prevY = e.clientY

  cameraRotation.value.y += dx * 0.005
  cameraRotation.value.x += dy * 0.005

  const limit = Math.PI / 2.2

  cameraRotation.value.x = Math.max(
    -limit,
    Math.min(limit, cameraRotation.value.x)
  )
}

function onMouseUp() {
  isDragging = false
}

function onWheel(e) {
  e.preventDefault()

  camera.fov += e.deltaY * 0.03
  camera.fov = Math.max(35, Math.min(100, camera.fov))
  camera.updateProjectionMatrix()
}

function onResize() {
  if (!camera || !renderer || !canvasRef.value) return

  const canvas = canvasRef.value

  camera.aspect = canvas.clientWidth / canvas.clientHeight
  camera.updateProjectionMatrix()

  renderer.setSize(canvas.clientWidth, canvas.clientHeight)
}

function handleRoomChange(hotspot) {
  const scenesParam = encodeURIComponent(JSON.stringify(normalizedScenes.value))

  window.location.href =
    `/vr-tour?image=${encodeURIComponent(hotspot.targetImage)}&scenes=${scenesParam}`
}

function zoomIn() {
  camera.fov = Math.max(35, camera.fov - 5)
  camera.updateProjectionMatrix()
}

function zoomOut() {
  camera.fov = Math.min(100, camera.fov + 5)
  camera.updateProjectionMatrix()
}

function lookUp() {
  cameraRotation.value.x -= 0.08
}

function lookDown() {
  cameraRotation.value.x += 0.08
}

function lookLeft() {
  cameraRotation.value.y -= 0.08
}

function lookRight() {
  cameraRotation.value.y += 0.08
}

function resetView() {
  cameraRotation.value.x = 0
  cameraRotation.value.y = 0

  camera.fov = 75
  camera.updateProjectionMatrix()
}

function destroyViewer() {
  cancelAnimationFrame(animationId)

  unbindEvents()

  if (renderer) renderer.dispose()
  if (sphere?.geometry) sphere.geometry.dispose()
  if (sphere?.material?.map) sphere.material.map.dispose()
  if (sphere?.material) sphere.material.dispose()

  scene = null
  camera = null
  renderer = null
  sphere = null
}

onMounted(async () => {
  await nextTick()
  initViewer()
})

watch(
  () => props.src,
  () => {
    initViewer()
  }
)

onBeforeUnmount(() => {
  destroyViewer()
})
</script>

<style scoped>
.vr-fullscreen{
  position:fixed;
  inset:0;
  background:#000;
  z-index:9999;
}

.vr-canvas{
  width:100%;
  height:100%;
  position:relative;
}

.vr-canvas-el{
  width:100%;
  height:100%;
  display:block;
}

.vr-hotspots{
  position:absolute;
  inset:0;
  pointer-events:none;
}

.vr-hotspot{
  position:absolute;
  pointer-events:auto;
  cursor:pointer;
  transform:translate(-50%, -50%) rotate(var(--hotspot-rotation, 0deg));
}

.vr-hotspot__arrow{
  width:56px;
  height:56px;
  border-radius:999px;
  background:#2563eb;
  color:#fff;
  display:flex;
  align-items:center;
  justify-content:center;
  font-size:22px;
  box-shadow:0 10px 30px rgba(0,0,0,.35);
  animation:pulse 2s infinite;
}

.vr-hotspot__label{
  margin-top:8px;
  background:rgba(0,0,0,.8);
  color:#fff;
  font-size:12px;
  padding:6px 12px;
  border-radius:20px;
  white-space:nowrap;
  text-align:center;
}

.vr-hotspot:hover .vr-hotspot__arrow{
  transform:scale(1.08);
  background:#16a34a;
}

.vr-controls{
  position:absolute;
  right:20px;
  bottom:20px;
  display:grid;
  grid-template-columns:repeat(3,44px);
  gap:8px;
  z-index:20;
}

.vr-btn{
  width:44px;
  height:44px;
  border:none;
  border-radius:10px;
  background:rgba(0, 0, 0, 0.747);
  color:#fff;
  cursor:pointer;
  font-size:18px;
}

.vr-btn:hover{
  background:rgba(255,255,255,.28);
}

.vr-loading,
.vr-error{
  position:absolute;
  inset:0;
  display:flex;
  align-items:center;
  justify-content:center;
  color:#fff;
  background:rgba(0,0,0,.75);
  z-index:30;
}

.vr-error{
  color:#ef4444;
}

@keyframes pulse{
  0%{transform:scale(1);}
  50%{transform:scale(1.08);}
  100%{transform:scale(1);}
}
</style>
