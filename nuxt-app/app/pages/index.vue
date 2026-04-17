<script setup lang="ts">
const localePath = useLocalePath();

await navigateTo(localePath("/home-page-01"), {
  redirectCode: 302,
  replace: true,
});
</script>
