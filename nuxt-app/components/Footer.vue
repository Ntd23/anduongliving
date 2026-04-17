<template>
  <footer style="background-color: #5f4d45; color: white; padding: 40px 20px 20px;">
    <div style="max-width: 1200px; margin: 0 auto;">
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; margin-bottom: 30px;">
        
        <!-- Display all footer widgets dynamically -->
        <div v-for="widget in widgets" :key="widget.id">
          <!-- Special handling for NewsletterWidget -->
          <div v-if="widget.widget_id === 'NewsletterWidget'">
            <div style="background-color: rgba(255,255,255,0.1); padding: 20px; border-radius: 8px;">
              <h4 style="margin-bottom: 15px; font-size: 16px; color: white;">
                {{ widget.data.title || 'Subscribe to our Newsletter' }}
              </h4>
              <form @submit.prevent="subscribeNewsletter" style="display: flex; flex-direction: column; gap: 10px;">
                <input 
                  v-model="newsletterEmail"
                  type="email" 
                  placeholder="Enter your email"
                  required
                  style="padding: 10px; border: none; border-radius: 4px; font-size: 14px;"
                />
                
                <button 
                  type="submit"
                  :disabled="newsletterLoading"
                  style="padding: 10px 20px; background-color: #e74c3c; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 14px;"
                  :style="{ backgroundColor: newsletterLoading ? '#95a5a6' : '#e74c3c', cursor: newsletterLoading ? 'not-allowed' : 'pointer' }"
                >
                  {{ newsletterLoading ? 'Subscribing...' : 'Subscribe' }}
                </button>
              </form>
              
              <div v-if="newsletterMessage" :style="{
                marginTop: '10px',
                padding: '10px',
                borderRadius: '4px',
                fontSize: '14px',
                backgroundColor: newsletterMessageType === 'success' ? 'rgba(46, 204, 113, 0.2)' : 'rgba(231, 76, 60, 0.2)',
                color: newsletterMessageType === 'success' ? '#2ecc71' : '#e74c3c'
              }">
                {{ newsletterMessage }}
              </div>
            </div>
          </div>
          
          <!-- CustomMenuWidget with menu data -->
          <div v-else-if="widget.widget_id === 'CustomMenuWidget' && widget.menu_data && widget.menu_data.nodes">
            <div>
              <h3 v-if="widget.data.name" style="margin-bottom: 15px; font-size: 24px;">{{ widget.data.name }}</h3>
              <div style="line-height: 1.8;">
                <ul style="list-style: none; padding: 0; margin: 0;">
                  <li v-for="node in widget.menu_data.nodes" :key="node.id" style="margin-bottom: 8px;">
                    <a 
                      :href="node.url" 
                      :target="node.target || '_self'"
                      style="color: white; text-decoration: none; transition: color 0.3s;"
                      @mouseover="$event.target.style.color='#3498db'"
                      @mouseout="$event.target.style.color='white'"
                    >
                      {{ node.title }}
                    </a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
          
          <!-- Contact Information Widget with Logo -->
          <div v-else-if="widget.data.phone_number || widget.data.email || widget.data.address">
            <div>
              <!-- Logo Image -->
              <div style="text-align: center; margin-bottom: 20px;">
                <img 
                  src="/images/logo.png" 
                  alt="An Duong Living Logo" 
                  style="max-width: 150px; height: auto; margin-bottom: 10px;"
                />
              </div>
              
              <h3 v-if="widget.data.name" style="margin-bottom: 15px; font-size: 18px; text-align: center;">{{ widget.data.name }}</h3>
              <div style="line-height: 1.8;">
                <!-- Display contact information -->
                <div v-if="widget.data.phone_number" style="margin-bottom: 8px;">
                  <strong>Phone:</strong> {{ widget.data.phone_number }}
                </div>
                <div v-if="widget.data.email" style="margin-bottom: 8px;">
                  <strong>Email:</strong> {{ widget.data.email }}
                </div>
                <div v-if="widget.data.address" style="margin-bottom: 8px;">
                  <strong>Address:</strong> {{ widget.data.address }}
                </div>
                <!-- Display other data fields -->
                <div v-for="(value, key) in widget.data" :key="key">
                  <div v-if="key !== 'id' && key !== 'name' && key !== 'title' && key !== 'menu_id' && key !== 'phone_number' && key !== 'email' && key !== 'address' && value">
                    <strong>{{ formatKey(key) }}:</strong> {{ formatValue(value) }}<br>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Regular widgets -->
          <div v-else>
            <h3 v-if="widget.data.name" style="margin-bottom: 15px; font-size: 18px;">{{ widget.data.name }}</h3>
            <div style="line-height: 1.8;">
              <!-- Display all data fields dynamically except id and name -->
              <div v-for="(value, key) in widget.data" :key="key">
                <div v-if="key !== 'id' && key !== 'name' && key !== 'title' && key !== 'menu_id' && value">
                  <strong>{{ formatKey(key) }}:</strong> {{ formatValue(value) }}<br>
                </div>
              </div>
            </div>
          </div>
        </div>
        
      </div>

      <!-- Copyright -->
      <div style="border-top: 1px solid #34495e; padding-top: 20px; text-align: center;">
        <p style="margin: 0; opacity: 0.8;">
          © {{ currentYear }} An Duong Living. All rights reserved.
        </p>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const widgets = ref([])
const loading = ref(true)
const currentYear = new Date().getFullYear()

// Newsletter specific variables
const newsletterEmail = ref('')
const newsletterLoading = ref(false)
const newsletterMessage = ref('')
const newsletterMessageType = ref('success')

// Helper function to format key names
const formatKey = (key) => {
  return key.charAt(0).toUpperCase() + key.slice(1).replace(/_/g, ' ')
}

// Helper function to format values
const formatValue = (value) => {
  if (typeof value === 'object') {
    return JSON.stringify(value, null, 2)
  }
  return value
}

const fetchFooterWidgets = async () => {
  try {
    loading.value = true
    
    // Fetch footer widgets with current locale
    const response = await $fetch('http://anduongliving.test/api/v1/widgets/sidebar/footer_sidebar', {
      headers: {
        'Accept': 'application/json',
        'X-API-KEY': '6bpm0t8Kk5NVnmJqOH8J7s0RGMoKdJIe'
      },
      query: {
        locale: 'vi' // You can make this dynamic based on current locale
      }
    })
    
    widgets.value = response.data || []
    console.log('Footer widgets:', response.data)
    
  } catch (error) {
    console.error('Error fetching footer widgets:', error)
  } finally {
    loading.value = false
  }
}

const subscribeNewsletter = async () => {
  if (!newsletterEmail.value) return
  
  try {
    newsletterLoading.value = true
    newsletterMessage.value = ''
    
    // Call newsletter API
    const response = await $fetch('http://anduongliving.test/api/v1/newsletter/subscribe', {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'X-API-KEY': '6bpm0t8Kk5NVnmJqOH8J7s0RGMoKdJIe',
        'Content-Type': 'application/json'
      },
      body: {
        email: newsletterEmail.value
      }
    })
    
    if (response.error) {
      throw new Error(response.message || 'Subscription failed')
    }
    
    newsletterMessage.value = response.message || 'Successfully subscribed!'
    newsletterMessageType.value = 'success'
    newsletterEmail.value = ''
    
  } catch (error) {
    newsletterMessage.value = error.message || 'Failed to subscribe. Please try again.'
    newsletterMessageType.value = 'error'
  } finally {
    newsletterLoading.value = false
  }
}

onMounted(() => {
  fetchFooterWidgets()
})
</script>
