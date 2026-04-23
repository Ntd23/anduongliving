<script setup lang="ts">
import { computed, ref } from "vue";
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

/* ── Mobile rail dot tracking ── */
const railRef = ref<HTMLElement | null>(null);
const activeDot = ref(0);
const itemCount = computed(() => section.value.items.length);

const onRailScroll = () => {
  const el = railRef.value;
  if (!el || !itemCount.value) return;
  const maxScroll = el.scrollWidth - el.clientWidth;
  if (maxScroll <= 0) return;
  const scrollRatio = el.scrollLeft / maxScroll;
  activeDot.value = Math.round(scrollRatio * (itemCount.value - 1));
};

const scrollToDot = (index: number) => {
  const el = railRef.value;
  if (!el || !itemCount.value) return;
  const cardWidth = el.scrollWidth / itemCount.value;
  el.scrollTo({ left: cardWidth * index, behavior: "smooth" });
};

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
          <figure
            v-if="section.backgroundImage1?.src"
            class="pricing-header__media"
            aria-hidden="true"
          >
            <img
              :src="resolveAsset(section.backgroundImage1.src) || section.backgroundImage1.src"
              :alt="section.backgroundImage1.alt || ''"
            >
          </figure>
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
          aria-hidden="true"
        >
          <img
            :src="resolveAsset(section.backgroundImage2.src) || section.backgroundImage2.src"
            :alt="section.backgroundImage2.alt || ''"
          >
        </div>

        <div ref="railRef" class="pricing-grid" @scroll="onRailScroll">
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

        <!-- Mobile dot indicators -->
        <div v-if="section.items.length > 1" class="pricing-dots-wrap">
          <div class="pricing-dots">
            <button
              v-for="(_, index) in section.items"
              :key="`pricing-dot-${index}`"
              type="button"
              class="pricing-dot"
              :class="{ 'is-active': index === activeDot }"
              :aria-label="`Go to plan ${index + 1}`"
              @click="scrollToDot(index)"
            />
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

/* ── Header ── */
.pricing-header {
  position: relative;
  z-index: 1;
  margin-bottom: clamp(2.5rem, 5vw, 4rem);
}

.pricing-header__panel {
  display: grid;
  grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
  gap: clamp(1.5rem, 4vw, 4rem);
  align-items: center;
  width: min(100%, 74rem);
  margin: 0 auto;
  padding: clamp(1.6rem, 3vw, 2.2rem) clamp(1.4rem, 3vw, 2.5rem);
  border-radius: 2rem;
  background: rgba(255, 250, 242, 0.62);
  backdrop-filter: blur(14px);
  border: 1px solid rgba(255, 255, 255, 0.72);
  box-shadow:
    0 24px 55px rgba(72, 49, 31, 0.08),
    inset 0 1px 0 rgba(255, 255, 255, 0.52);
}

.pricing-header__panel.has-header-media {
  position: relative;
  overflow: visible;
}

.pricing-header__media {
  position: absolute;
  top: -2rem;
  left: -2.5rem;
  width: clamp(10rem, 16vw, 14rem);
  height: clamp(8rem, 13vw, 11rem);
  margin: 0;
  border-radius: 1.7rem;
  opacity: 0.08;
  transform: rotate(-7deg);
  box-shadow: 0 24px 55px rgba(72, 49, 31, 0.12);
  overflow: hidden;
  pointer-events: none;
}

.pricing-header__media img {
  display: block;
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.pricing-header__intro {
  position: relative;
  z-index: 1;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  justify-content: center;
  min-height: auto;
  max-width: 29rem;
  padding-left: 0;
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
  font-size: clamp(2.5rem, 4.3vw, 4rem);
  line-height: 0.95;
  font-weight: 600;
  text-wrap: balance;
}

.pricing-description {
  position: relative;
  z-index: 1;
  max-width: 33rem;
  margin: 0 0 0 auto;
  color: rgba(47, 36, 29, 0.76);
  font-size: 1rem;
  line-height: 1.8;
  text-align: left;
}

/* ── Cards shell ── */
.pricing-cards-shell {
  position: relative;
  z-index: 1;
  padding-top: 1.2rem;
}

.pricing-cards-shell__media {
  position: absolute;
  right: -1.25rem;
  bottom: -1.5rem;
  width: clamp(8rem, 12vw, 11rem);
  height: clamp(8rem, 12vw, 11rem);
  border-radius: 2rem;
  opacity: 0.06;
  transform: rotate(7deg);
  box-shadow: 0 24px 60px rgba(72, 49, 31, 0.12);
  pointer-events: none;
  overflow: hidden;
  z-index: 0;
}

.pricing-cards-shell__media img {
  display: block;
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.pricing-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(17rem, 1fr));
  gap: 1rem;
  align-items: stretch;
  position: relative;
  z-index: 1;
}

/* ── Card ── */
.pricing-card {
  position: relative;
  display: flex;
  flex-direction: column;
  min-height: 28rem;
  padding: 1.55rem 1.15rem 1.1rem;
  overflow: hidden;
  border-radius: 2rem;
  background:
    linear-gradient(180deg, rgba(255, 251, 245, 0.95), rgba(252, 244, 232, 0.88));
  box-shadow:
    0 24px 60px rgba(56, 38, 25, 0.08),
    0 4px 12px rgba(56, 38, 25, 0.04),
    inset 0 1px 0 rgba(255, 255, 255, 0.75);
  border: 1px solid rgba(185, 161, 129, 0.2);
  backdrop-filter: blur(6px);
  transition:
    transform 0.32s cubic-bezier(0.4, 0, 0.2, 1),
    box-shadow 0.32s ease,
    border-color 0.32s ease;
}

.pricing-card:hover {
  box-shadow:
    0 36px 80px rgba(56, 38, 25, 0.14),
    0 6px 16px rgba(56, 38, 25, 0.06),
    inset 0 1px 0 rgba(255, 255, 255, 0.82);
  border-color: rgba(185, 161, 129, 0.35);
}

.pricing-card--popular {
  border-color: rgba(122, 99, 61, 0.4);
  box-shadow:
    0 28px 68px rgba(56, 38, 25, 0.12),
    0 0 0 1px rgba(122, 99, 61, 0.08),
    inset 0 1px 0 rgba(255, 255, 255, 0.82);
  transform: translateY(-0.35rem);
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

/* ── Card header ── */
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
  font-size: 1.24rem;
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
  font-size: clamp(1.9rem, 2.6vw, 2.6rem);
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
  line-height: 1.5;
  display: -webkit-box;
  -webkit-line-clamp: 4;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* ── Features ── */
.pricing-features {
  list-style: none;
  padding: 0;
  margin: 0 0 1rem;
  position: relative;
  z-index: 1;
  flex: 1;
  overflow: hidden;
}

.pricing-feature {
  position: relative;
  padding: 0.62rem 0 0.62rem 1.2rem;
  color: rgba(47, 36, 29, 0.82);
  font-size: 0.91rem;
  line-height: 1.48;
  border-bottom: 1px solid rgba(109, 88, 58, 0.1);
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.pricing-feature::before {
  content: "";
  position: absolute;
  top: 0.92rem;
  left: 0;
  width: 0.55rem;
  height: 0.55rem;
  border-radius: 999px;
  background: linear-gradient(135deg, var(--retreat-olive, #6f7553), var(--retreat-clay, #b9825a));
  box-shadow: 0 0 0 3px rgba(111, 117, 83, 0.08);
}

.pricing-feature:last-child {
  border-bottom: none;
}

/* ── Button ── */
.pricing-button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  min-height: 3.1rem;
  padding: 0.72rem 1.1rem;
  background: linear-gradient(135deg, #32231a, #4a3628);
  color: #fff8ef;
  text-decoration: none;
  border-radius: 999px;
  font-weight: 600;
  text-align: center;
  border: 1px solid rgba(50, 35, 26, 0.15);
  box-shadow: 0 16px 32px rgba(50, 35, 26, 0.14);
  transition:
    transform 0.25s ease,
    background-color 0.25s ease,
    box-shadow 0.25s ease;
}

.pricing-button:hover {
  background: linear-gradient(135deg, #4a3628, #60543e);
  box-shadow: 0 20px 40px rgba(50, 35, 26, 0.18);
  color: #fffaf3;
}

.pricing-button--popular {
  background: linear-gradient(135deg, #7a6340, #9f7f55);
}

.pricing-button--popular:hover {
  background: linear-gradient(135deg, #665233, #876a44);
}

/* ── Mobile dots ── */
.pricing-dots-wrap {
  display: none;
}

/* ── Responsive ── */
@media (max-width: 1279px) {
  .pricing-header__panel {
    grid-template-columns: 1fr;
    gap: 1rem;
  }

  .pricing-header__media {
    top: -1rem;
    left: -1rem;
    width: 9rem;
    height: 7rem;
  }

  .pricing-description {
    margin: 0;
    max-width: 44rem;
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
    scrollbar-width: none;
  }

  .pricing-grid::-webkit-scrollbar {
    display: none;
  }

  .pricing-card {
    padding: 1.45rem 1.1rem 1.1rem;
    min-height: 26rem;
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

  .pricing-dots-wrap {
    display: flex;
    justify-content: center;
    padding: 1.25rem 0 0;
  }

  .pricing-dots {
    display: flex;
    align-items: center;
    gap: 0.55rem;
    padding: 0.4rem 0.7rem;
    border-radius: 999px;
    background: rgba(47, 36, 29, 0.06);
  }

  .pricing-dot {
    position: relative;
    width: 0.5rem;
    height: 0.5rem;
    padding: 0;
    border: 0;
    border-radius: 999px;
    background: rgba(47, 36, 29, 0.2);
    cursor: pointer;
    transition:
      transform 0.2s ease,
      background-color 0.2s ease;
  }

  .pricing-dot::before {
    content: "";
    position: absolute;
    inset: -0.75rem;
  }

  .pricing-dot.is-active {
    background: rgba(47, 36, 29, 0.65);
    transform: scale(1.35);
  }
}
</style>