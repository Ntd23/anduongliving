<template>
  <div class="vr-tour-page">
    <VrViewer :src="image" :scenes="scenes" />
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import VrViewer from '~/components/VrViewer.vue';

const route = useRoute();
const image = ref('');
const scenes = ref([]);

function normalizeScenePayload(payload) {
  if (!Array.isArray(payload)) {
    return [];
  }

  return payload
    .map((scene, sceneIndex) => {
      if (!scene?.image) {
        return null;
      }

      const hotspots = Array.isArray(scene.hotspots)
        ? scene.hotspots
            .map((hotspot, hotspotIndex) => {
              const targetImage = hotspot?.targetImage || hotspot?.image;

              if (!targetImage || targetImage === scene.image) {
                return null;
              }

              return {
                id: hotspot?.id ?? `scene-${sceneIndex + 1}-hotspot-${hotspotIndex + 1}`,
                name: hotspot?.name || `Gian ${hotspotIndex + 1}`,
                targetImage,
                yaw: Number(hotspot?.yaw ?? 0),
                pitch: Number(hotspot?.pitch ?? -10)
              };
            })
            .filter(Boolean)
        : [];

      return {
        id: scene.id ?? `scene-${sceneIndex + 1}`,
        name: scene.name || `Gian ${sceneIndex + 1}`,
        image: scene.image,
        hotspots
      };
    })
    .filter(Boolean);
}

function convertLegacyRoomsToScenes(payload) {
  if (!Array.isArray(payload)) {
    return [];
  }

  const normalizedRooms = payload
    .map((room, roomIndex) => {
      if (!room?.image) {
        return null;
      }

      return {
        id: room.id ?? `legacy-room-${roomIndex + 1}`,
        name: room.name || `Gian ${roomIndex + 1}`,
        image: room.image,
        yaw: Number(room.yaw ?? 0),
        pitch: Number(room.pitch ?? -10)
      };
    })
    .filter(Boolean);

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
  }));
}

function parseTourQuery() {
  image.value = route.query.image ? decodeURIComponent(route.query.image) : '';

  if (route.query.scenes) {
    try {
      scenes.value = normalizeScenePayload(JSON.parse(decodeURIComponent(route.query.scenes)));
      return;
    } catch (error) {
      console.error('Failed to parse scenes data:', error);
    }
  }

  if (route.query.rooms) {
    try {
      scenes.value = convertLegacyRoomsToScenes(JSON.parse(decodeURIComponent(route.query.rooms)));
    } catch (error) {
      console.error('Failed to parse rooms data:', error);
      scenes.value = [];
    }
    return;
  }

  scenes.value = [];
}

onMounted(() => {
  parseTourQuery();
});

watch(
  () => route.query,
  () => {
    parseTourQuery();
  }
);

// SEO Meta
useHead({
  title: 'VR Tour - Anduong Living',
  meta: [
    { name: 'description', content: 'Trải nghiệm VR tour 360 độ tại Anduong Living' },
    { name: 'keywords', content: 'VR tour, 360 tour, thực tế ảo, anduong living' }
  ]
});

// Disable layout for VR tour page
definePageMeta({
  layout: false
});
</script>

<style>
/* Hide scrollbars for full-screen VR experience */
body {
  overflow: hidden;
  margin: 0;
  padding: 0;
}

html {
  overflow: hidden;
}

/* Hide all layout elements for VR tour */
header, nav, footer, .layout-header, .layout-nav, .layout-footer {
  display: none !important;
}
</style>

<style scoped>
.vr-tour-page {
  width: 100vw;
  height: 100vh;
  position: fixed;
  top: 0;
  left: 0;
  background: #000;
  z-index: 9999;
}
</style>
