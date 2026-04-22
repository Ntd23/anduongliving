<script setup lang="ts">
import { computed } from "vue";
import {
  parsePricingBlock,
  type PricingSectionData,
  type ShortcodeBlock,
} from "~/utils/shortcode";
import { useResolvedCmsLink } from "~/composables/useResolvedCmsLink";
import { useResolvedCmsAsset } from "~/composables/useResolvedCmsAsset";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";

const props = defineProps<{
  block: ShortcodeBlock;
}>();

const section = computed<PricingSectionData>(() => parsePricingBlock(props.block.raw));
const sanitizedHtml = useSanitizedCmsHtml(() => props.block.raw);
const resolveLink = useResolvedCmsLink();
const resolveAsset = useResolvedCmsAsset();

const sectionStyle = computed(() =>
  section.value.backgroundColor
    ? { background: section.value.backgroundColor }
    : undefined,
);

const headerBackgroundStyle = computed(() =>
  section.value.backgroundImage1?.src
    ? { backgroundImage: `url(${resolveAsset(section.value.backgroundImage1.src) || section.value.backgroundImage1.src})` }
    : undefined,
);

const cardsBackgroundStyle = computed(() =>
  section.value.backgroundImage2?.src
    ? { backgroundImage: `url(${resolveAsset(section.value.backgroundImage2.src) || section.value.backgroundImage2.src})` }
    : undefined,
);
</script>

<template>
  <section
    v-if="section.title || section.subtitle || section.description || section.items.length"
    class="shortcode-pricing"
    :style="sectionStyle"
  >
    <div class="container">
      <div v-if="section.title || section.subtitle || section.description" class="pricing-header">
        <div class="pricing-header__panel" :class="{ 'has-header-media': section.backgroundImage1?.src }">
          <div
            v-if="section.backgroundImage1?.src"
            class="pricing-header__media"
            :style="headerBackgroundStyle"
            aria-hidden="true"
          />
          <div class="pricing-header__intro">
            <p v-if="section.subtitle" class="pricing-subtitle">
              {{ section.subtitle }}
            </p>
            <h2 v-if="section.title" class="pricing-title">
              {{ section.title }}
            </h2>
          </div>

          <p v-if="section.description" class="pricing-description">
            {{ section.description }}
          </p>
        </div>
      </div>

      <div v-if="section.items.length" class="pricing-cards-shell">
        <div
          v-if="section.backgroundImage2?.src"
          class="pricing-cards-shell__media"
          :style="cardsBackgroundStyle"
          aria-hidden="true"
        />

        <div class="pricing-grid">
          <div
            v-for="item in section.items"
            :key="item.title"
            class="pricing-card"
            :class="{ 'pricing-card--popular': item.isPopular }"
          >
            <div class="pricing-card__glow" aria-hidden="true" />

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
              v-if="resolveLink(item.buttonUrl)?.isInternal"
              :to="resolveLink(item.buttonUrl)!.href"
              class="pricing-button"
              :class="{ 'pricing-button--popular': item.isPopular }"
            >
              {{ item.buttonLabel }}
            </NuxtLink>
            <a
              v-else
              :href="item.buttonUrl"
              class="pricing-button"
              :class="{ 'pricing-button--popular': item.isPopular }"
            >
              {{ item.buttonLabel }}
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section v-else class="shortcode-pricing">
    <div class="container" v-html="sanitizedHtml" />
  </section>
</template>

<style scoped>
.shortcode-pricing {
  position: relative;
  overflow: hidden;
  padding: clamp(4.5rem, 9vw, 7rem) 0;
  background:
    radial-gradient(circle at top left, rgba(212, 194, 164, 0.26), transparent 32%),
    linear-gradient(180deg, #fbf6ee 0%, #efe4d4 100%);
}

.container {
  width: min(1200px, calc(100% - 48px));
  margin: 0 auto;
}

.pricing-header {
  position: relative;
  z-index: 1;
  margin-bottom: clamp(2.5rem, 5vw, 4rem);
}

.pricing-header__panel {
  display: grid;
  grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
  gap: clamp(1.5rem, 4vw, 4rem);
  align-items: end;
  width: min(100%, 74rem);
  margin: 0 auto;
  padding: clamp(1.6rem, 3vw, 2.2rem) clamp(1.4rem, 3vw, 2.5rem);
  border-radius: 2rem;
  background: rgba(255, 250, 242, 0.62);
  backdrop-filter: blur(14px);
  border: 1px solid rgba(255, 255, 255, 0.72);
  box-shadow: 0 24px 55px rgba(72, 49, 31, 0.08);
}

.pricing-header__panel.has-header-media {
  position: relative;
  overflow: hidden;
}

.pricing-header__media {
  position: absolute;
  inset: 1rem auto 1rem 1rem;
  width: clamp(12rem, 22vw, 20rem);
  border-radius: 1.7rem;
  opacity: 0.18;
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
  transform: rotate(-7deg);
  box-shadow: 0 24px 55px rgba(72, 49, 31, 0.12);
}

.pricing-header__intro {
  position: relative;
  z-index: 1;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  max-width: 29rem;
}

.pricing-subtitle {
  margin: 0 0 0.9rem;
  color: #8c6a43;
  font-size: 0.8rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.28em;
}

.pricing-title {
  margin: 0;
  color: #2f241d;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: clamp(3rem, 5.4vw, 5rem);
  line-height: 0.92;
  font-weight: 600;
}

.pricing-description {
  position: relative;
  z-index: 1;
  max-width: 40rem;
  margin: 0 0 0 auto;
  color: rgba(47, 36, 29, 0.76);
  font-size: 1.03rem;
  line-height: 1.9;
  text-align: left;
}

.pricing-cards-shell {
  position: relative;
  z-index: 1;
  padding-top: 1.2rem;
}

.pricing-cards-shell__media {
  position: absolute;
  inset: auto -2rem -2rem auto;
  width: clamp(12rem, 18vw, 18rem);
  height: clamp(12rem, 18vw, 18rem);
  border-radius: 2rem;
  opacity: 0.14;
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
  transform: rotate(7deg);
  box-shadow: 0 24px 60px rgba(72, 49, 31, 0.12);
  pointer-events: none;
}

.pricing-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 1rem;
  align-items: stretch;
  position: relative;
  z-index: 1;
}

.pricing-card {
  position: relative;
  display: flex;
  flex-direction: column;
  min-height: 100%;
  padding: 1.55rem 1.15rem 1.1rem;
  overflow: hidden;
  border-radius: 2rem;
  background:
    linear-gradient(180deg, rgba(255, 251, 245, 0.92), rgba(252, 244, 232, 0.82));
  box-shadow:
    0 24px 60px rgba(56, 38, 25, 0.08),
    inset 0 1px 0 rgba(255, 255, 255, 0.75);
  border: 1px solid rgba(185, 161, 129, 0.22);
  transition: transform 0.28s ease, box-shadow 0.28s ease, border-color 0.28s ease;
}

.pricing-card:hover {
  transform: translateY(-6px);
  box-shadow:
    0 30px 70px rgba(56, 38, 25, 0.12),
    inset 0 1px 0 rgba(255, 255, 255, 0.82);
}

.pricing-card--popular {
  border-color: rgba(122, 99, 61, 0.4);
  transform: translateY(-0.35rem);
}

.pricing-card--popular::before {
  content: "Signature";
  position: absolute;
  top: 1.05rem;
  left: 50%;
  transform: translateX(-50%);
  background: rgba(97, 78, 49, 0.92);
  color: #fff8ef;
  padding: 0.35rem 0.8rem;
  border-radius: 999px;
  font-size: 0.72rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.18em;
}

.pricing-card__glow {
  position: absolute;
  inset: auto auto -3rem -3rem;
  width: 8rem;
  height: 8rem;
  border-radius: 999px;
  background: radial-gradient(circle, rgba(193, 163, 118, 0.22), transparent 70%);
  pointer-events: none;
}

.pricing-card-header {
  position: relative;
  z-index: 1;
  margin-bottom: 1.1rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid rgba(109, 88, 58, 0.12);
  text-align: center;
}

.pricing-card-title {
  margin: 0 0 0.7rem;
  color: #2f241d;
  font-size: 1.32rem;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-weight: 600;
  line-height: 1.02;
}

.pricing-card-price {
  display: flex;
  align-items: baseline;
  justify-content: center;
  gap: 0.28rem;
  flex-wrap: wrap;
}

.pricing-currency {
  color: #876641;
  font-size: 1.05rem;
  font-weight: 400;
}

.pricing-amount {
  color: #2a2018;
  font-size: clamp(2.2rem, 3.1vw, 3.1rem);
  font-weight: 700;
  line-height: 0.95;
}

.pricing-period {
  color: rgba(47, 36, 29, 0.58);
  font-size: 0.92rem;
  font-weight: 400;
}

.pricing-card-description {
  margin: 0.75rem 0 0;
  color: rgba(47, 36, 29, 0.72);
  font-size: 0.89rem;
  line-height: 1.6;
}

.pricing-features {
  list-style: none;
  padding: 0;
  margin: 0 0 1rem;
  position: relative;
  z-index: 1;
  flex: 1;
}

.pricing-feature {
  position: relative;
  padding: 0.62rem 0 0.62rem 1rem;
  color: rgba(47, 36, 29, 0.82);
  font-size: 0.91rem;
  line-height: 1.48;
  border-bottom: 1px solid rgba(109, 88, 58, 0.1);
}

.pricing-feature::before {
  content: "";
  position: absolute;
  top: 0.95rem;
  left: 0;
  width: 0.42rem;
  height: 0.42rem;
  border-radius: 999px;
  background: #a98257;
}

.pricing-feature:last-child {
  border-bottom: none;
}

.pricing-button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  min-height: 3rem;
  padding: 0.72rem 1.1rem;
  background: #32231a;
  color: #fff8ef;
  text-decoration: none;
  border-radius: 999px;
  font-weight: 600;
  text-align: center;
  border: 1px solid rgba(50, 35, 26, 0.15);
  box-shadow: 0 16px 28px rgba(50, 35, 26, 0.12);
  transition: transform 0.25s ease, background-color 0.25s ease, color 0.25s ease;
}

.pricing-button:hover {
  background: #60543e;
  transform: translateY(-2px);
  color: #fffaf3;
}

.pricing-button--popular {
  background: #7a6340;
}

.pricing-button--popular:hover {
  background: #665233;
}

@media (max-width: 1279px) {
  .pricing-header__panel {
    grid-template-columns: 1fr;
    gap: 1rem;
  }

  .pricing-header__media {
    inset: 1rem auto auto 1rem;
    width: 12rem;
    height: 9rem;
  }

  .pricing-description {
    margin: 0;
    max-width: 44rem;
  }

  .pricing-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 768px) {
  .shortcode-pricing {
    padding: 4rem 0;
  }

  .pricing-header {
    margin-bottom: 2.2rem;
  }

  .pricing-grid {
    display: grid;
    grid-auto-flow: column;
    grid-auto-columns: minmax(84%, 84%);
    grid-template-columns: none;
    gap: 1rem;
    overflow-x: auto;
    padding-bottom: 0.55rem;
    scroll-snap-type: x mandatory;
    scrollbar-width: thin;
  }

  .pricing-card {
    padding: 1.45rem 1.1rem 1.1rem;
    transform: none;
    scroll-snap-align: start;
  }

  .pricing-amount {
    font-size: 2.6rem;
  }

  .pricing-card--popular {
    transform: none;
  }

  .pricing-header__panel {
    padding: 1.45rem 1.15rem;
  }

  .pricing-header__media,
  .pricing-cards-shell__media {
    display: none;
  }

  .pricing-title {
    font-size: clamp(2.4rem, 11vw, 3.8rem);
    line-height: 0.95;
  }

  .pricing-description {
    font-size: 0.98rem;
    line-height: 1.78;
  }

  .container {
    width: min(100%, calc(100% - 1.25rem));
  }
}
</style>
