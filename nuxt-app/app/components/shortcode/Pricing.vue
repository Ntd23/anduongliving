<script setup lang="ts">
import { computed } from "vue";
import {
  parsePricingBlock,
  type PricingSectionData,
  type ShortcodeBlock,
} from "~/utils/shortcode";

const props = defineProps<{
  block: ShortcodeBlock;
}>();

const section = computed<PricingSectionData>(() => parsePricingBlock(props.block.raw));

const sectionStyle = computed(() =>
  section.value.backgroundColor
    ? { background: section.value.backgroundColor }
    : undefined,
);
</script>

<template>
  <section class="shortcode-pricing" :style="sectionStyle">
    <div class="shortcode-pricing__background">
      <div v-if="section.backgroundImage1" class="pricing-bg-image pricing-bg-image--one">
        <img :src="section.backgroundImage1.src" :alt="section.backgroundImage1.alt || ''" />
      </div>
      <div v-if="section.backgroundImage2" class="pricing-bg-image pricing-bg-image--two">
        <img :src="section.backgroundImage2.src" :alt="section.backgroundImage2.alt || ''" />
      </div>
    </div>
    <div class="container">
      <div v-if="section.title || section.subtitle || section.description" class="pricing-header">
        <pre style="white-space: pre-wrap; font-size: 12px;">
{{ JSON.stringify(section, null, 2) }}
</pre>
        <p v-if="section.subtitle" class="pricing-subtitle">
          {{ section.subtitle }}
        </p>
        <h2 v-if="section.title" class="pricing-title">
          {{ section.title }}
        </h2>
        <p v-if="section.description" class="pricing-description">
          {{ section.description }}
        </p>
      </div>

      <div v-if="section.items.length" class="pricing-grid">
        <div
          v-for="item in section.items"
          :key="item.title"
          class="pricing-card"
          :class="{ 'pricing-card--popular': item.isPopular }"
        >
          <div class="pricing-card-header">
            <h3 class="pricing-card-title">{{ item.title }}</h3>
            <div class="pricing-card-price">
              <span class="pricing-currency">{{ item.currency }}</span>
              <span class="pricing-amount">{{ item.price }}</span>
              <span class="pricing-period">{{ item.period }}</span>
            </div>
            <p v-if="item.description" class="pricing-card-description">
              {{ item.description }}
            </p>
          </div>

          <ul v-if="item.features.length" class="pricing-features">
            <li v-for="feature in item.features" :key="feature" class="pricing-feature">
              {{ feature }}
            </li>
          </ul>

          <NuxtLink
            :to="item.buttonUrl"
            class="pricing-button"
            :class="{ 'pricing-button--popular': item.isPopular }"
          >
            {{ item.buttonLabel }}
          </NuxtLink>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.shortcode-pricing {
  position: relative;
  overflow: hidden;
  padding: 72px 0;
  background: #f8f9fa;
}

.shortcode-pricing__background {
  position: absolute;
  inset: 0;
  pointer-events: none;
}

.pricing-bg-image {
  position: absolute;
  opacity: 0.2;
  pointer-events: none;
}

.pricing-bg-image--one {
  top: 0;
  left: 0;
  width: 240px;
}

.pricing-bg-image--two {
  bottom: 0;
  right: 0;
  width: 280px;
}

.pricing-bg-image img {
  display: block;
  width: 100%;
  height: auto;
}

.container {
  width: min(1200px, calc(100% - 48px));
  margin: 0 auto;
}

.pricing-header {
  text-align: center;
  margin-bottom: 64px;
}

.pricing-subtitle {
  margin: 0 0 16px;
  color: #6c757d;
  font-size: 14px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.1em;
}

.pricing-title {
  margin: 0 0 24px;
  color: #212529;
  font-size: clamp(2.5rem, 4vw, 3.5rem);
  line-height: 1.2;
  font-weight: 700;
}

.pricing-description {
  margin: 0;
  color: #6c757d;
  font-size: 18px;
  line-height: 1.6;
  max-width: 600px;
  margin: 0 auto;
}

.pricing-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 32px;
  align-items: start;
}

.pricing-card {
  background: #fff;
  border-radius: 12px;
  padding: 40px 32px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  border: 2px solid transparent;
  transition: all 0.3s ease;
}

.pricing-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.pricing-card--popular {
  border-color: #007bff;
  position: relative;
}

.pricing-card--popular::before {
  content: "Most Popular";
  position: absolute;
  top: -12px;
  left: 50%;
  transform: translateX(-50%);
  background: #007bff;
  color: white;
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.pricing-card-header {
  text-align: center;
  margin-bottom: 32px;
}

.pricing-card-title {
  margin: 0 0 16px;
  color: #212529;
  font-size: 24px;
  font-weight: 600;
}

.pricing-card-price {
  display: flex;
  align-items: baseline;
  justify-content: center;
  gap: 4px;
}

.pricing-currency {
  color: #6c757d;
  font-size: 20px;
  font-weight: 400;
}

.pricing-amount {
  color: #212529;
  font-size: 48px;
  font-weight: 700;
  line-height: 1;
}

.pricing-period {
  color: #6c757d;
  font-size: 16px;
  font-weight: 400;
}

.pricing-card-description {
  margin: 16px 0 0;
  padding: 16px 0 0;
  border-top: 1px solid #e9ecef;
  color: #495057;
  font-size: 14px;
  line-height: 1.6;
}

.pricing-features {
  list-style: none;
  padding: 0;
  margin: 0 0 32px;
}

.pricing-feature {
  padding: 8px 0;
  color: #495057;
  font-size: 16px;
  line-height: 1.5;
  border-bottom: 1px solid #e9ecef;
}

.pricing-feature:last-child {
  border-bottom: none;
}

.pricing-button {
  display: inline-block;
  width: 100%;
  padding: 12px 24px;
  background: #007bff;
  color: white;
  text-decoration: none;
  border-radius: 8px;
  font-weight: 600;
  text-align: center;
  transition: all 0.3s ease;
}

.pricing-button:hover {
  background: #0056b3;
  transform: translateY(-2px);
}

.pricing-button--popular {
  background: #007bff;
}

.pricing-button--popular:hover {
  background: #0056b3;
}

@media (max-width: 768px) {
  .shortcode-pricing {
    padding: 48px 0;
  }

  .pricing-bg-image {
    display: none;
  }

  .pricing-header {
    margin-bottom: 48px;
  }

  .pricing-grid {
    grid-template-columns: 1fr;
    gap: 24px;
  }

  .pricing-card {
    padding: 32px 24px;
  }

  .pricing-amount {
    font-size: 36px;
  }
}
</style>