<template>
  <div style="padding: 20px; max-width: 800px; margin: 0 auto;">
    <h1 style="color: #333; margin-bottom: 20px;">API Testing</h1>
    
    <div style="margin-bottom: 20px;">
      <input 
        v-model="apiUrl" 
        type="text" 
        placeholder="Enter API URL"
        style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; margin-bottom: 10px;"
      />
      <input 
        v-model="apiKey" 
        type="text" 
        placeholder="Enter API Key"
        style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px;"
      />
    </div>
    
    <div style="margin-bottom: 20px;">
      <button 
        @click="callApi"
        :disabled="loading"
        style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; margin-right: 10px;"
      >
        {{ loading ? 'Loading...' : 'Call API' }}
      </button>
      
      <button 
        @click="clearData"
        style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;"
      >
        Clear
      </button>
    </div>
    
    <div v-if="error" style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
      <strong>Error:</strong> {{ error }}
    </div>
    
    <div v-if="data" style="background: #f8f9fa; padding: 15px; border-radius: 4px;">
      <h3 style="margin-bottom: 10px;">JSON Response:</h3>
      <pre style="background: #e9ecef; padding: 10px; border-radius: 4px; overflow-x: auto; white-space: pre-wrap;">{{ JSON.stringify(data, null, 2) }}</pre>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const apiUrl = ref('http://anduongliving.test/api/v1/pages')
const apiKey = ref('')
const data = ref(null)
const error = ref(null)
const loading = ref(false)

const callApi = async () => {
  loading.value = true
  data.value = null
  error.value = null
  
  try {
    const result = await $fetch(apiUrl.value, {
      headers: {
        'Accept': 'application/json',
        'x-api-key': apiKey.value
      }
    })
    data.value = result
  } catch (err) {
    error.value = err.message || 'API call failed'
  } finally {
    loading.value = false
  }
}

const clearData = () => {
  data.value = null
  error.value = null
}
</script>
