<script setup lang="ts">
import { parseFaqsBlock, type ShortcodeBlock } from "~/utils/shortcode";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";

const props = defineProps<{ block: ShortcodeBlock }>();
const section = computed(() => parseFaqsBlock(props.block.raw));
const sanitizedHtml = useSanitizedCmsHtml(() => props.block.raw);
const openId = ref<string | null>(section.value.items[0]?.id || null);

watch(
  () => section.value.items,
  (items) => {
    openId.value = items[0]?.id || null;
  },
  { deep: true },
);
</script>

<template>
  <section v-if="section.items.length" class="shortcode-faqs-native">
    <div class="container faqs-shell">
      <article v-for="item in section.items" :key="item.id" class="faq-card">
        <button type="button" class="faq-card__button" @click="openId = openId === item.id ? null : item.id">
          <span>{{ item.question }}</span>
          <span>{{ openId === item.id ? "−" : "+" }}</span>
        </button>
        <div v-if="openId === item.id" class="faq-card__answer">
          {{ item.answer }}
        </div>
      </article>
    </div>
  </section>

  <section v-else class="shortcode-faqs-native">
    <div v-html="sanitizedHtml" />
  </section>
</template>

<style scoped>
.shortcode-faqs-native {
  padding: clamp(4rem, 8vw, 7rem) 0;
  background: linear-gradient(180deg, #faf5ee, #efe5d7);
}
.faqs-shell {
  display: grid;
  gap: 1rem;
  grid-template-columns: repeat(2, minmax(0, 1fr));
}
.faq-card {
  border-radius: 1.4rem;
  background: rgba(255, 252, 246, 0.9);
  box-shadow: 0 16px 40px rgba(48, 35, 27, 0.07);
}
.faq-card__button {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  padding: 1.2rem 1.3rem;
  border: 0;
  background: transparent;
  color: #2f241d;
  text-align: left;
  font-weight: 600;
}
.faq-card__answer {
  padding: 0 1.3rem 1.2rem;
  color: rgba(47, 36, 29, 0.76);
  line-height: 1.85;
}
@media (max-width: 991px) {
  .faqs-shell {
    grid-template-columns: 1fr;
  }
}
</style>
