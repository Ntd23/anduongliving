<template>
  <div class="google-maps-container">
    <div class="google-map-iframe" :style="{ marginBottom: '20px' }">
      <iframe
        :src="mapData.iframeSrc"
        :style="{ 
          height: mapData.height, 
          width: mapData.width, 
          maxWidth: '100%' 
        }"
        :title="mapData.title"
        frameborder="0"
        scrolling="no"
        marginheight="0"
        marginwidth="0"
      ></iframe>
    </div>
    
    <div v-if="mapData.address" class="google-maps-address">
      <h3>Địa chỉ</h3>
      <p>{{ mapData.address }}</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { parseGoogleMapBlock, type GoogleMapSectionData } from '~/utils/shortcode'

const props = defineProps<{
  block: {
    raw: string
  }
}>()

const mapData = computed<GoogleMapSectionData>(() => {
  return parseGoogleMapBlock(props.block.raw)
})
</script>

<style scoped>
.google-maps-container {
  width: 100%;
}

.google-map-iframe {
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.google-maps-address {
  margin-top: 16px;
  padding: 16px;
  background: #f8f9fa;
  border-radius: 8px;
  border-left: 4px solid #2563eb;
}

.google-maps-address h3 {
  margin: 0 0 8px 0;
  font-size: 16px;
  font-weight: 600;
  color: #1f2937;
}

.google-maps-address p {
  margin: 0;
  color: #6b7280;
  font-size: 14px;
  line-height: 1.5;
}

@media (max-width: 768px) {
  .google-maps-address {
    padding: 12px;
  }
  
  .google-maps-address h3 {
    font-size: 14px;
  }
  
  .google-maps-address p {
    font-size: 13px;
  }
}
</style>