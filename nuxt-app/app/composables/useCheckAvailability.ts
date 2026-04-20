import { computed, ref, toValue, watchEffect, type MaybeRefOrGetter } from "vue";

const BOTBLE_DATE_FORMATS = ["d-m-Y", "m-d-Y", "Y-m-d", "d/m/Y", "m/d/Y", "Y/m/d"] as const;

type CheckAvailabilityMeta = {
  action_url?: string | null;
  date_format?: string | null;
  minimum_adults?: number | null;
  maximum_adults?: number | null;
  default_children?: number | null;
  default_rooms?: number | null;
};

const pad = (value: number) => String(value).padStart(2, "0");

const formatBotbleDate = (isoDate: string, format: string) => {
  const date = new Date(`${isoDate}T00:00:00`);

  if (Number.isNaN(date.getTime())) {
    return isoDate;
  }

  const day = pad(date.getDate());
  const month = pad(date.getMonth() + 1);
  const year = String(date.getFullYear());

  switch (format) {
    case "d-m-Y":
      return `${day}-${month}-${year}`;
    case "m-d-Y":
      return `${month}-${day}-${year}`;
    case "Y-m-d":
      return `${year}-${month}-${day}`;
    case "d/m/Y":
      return `${day}/${month}/${year}`;
    case "m/d/Y":
      return `${month}/${day}/${year}`;
    case "Y/m/d":
      return `${year}/${month}/${day}`;
    default:
      return `${day}-${month}-${year}`;
  }
};

const addDays = (date: Date, days: number) => {
  const next = new Date(date);
  next.setDate(next.getDate() + days);
  return next;
};

const toIsoDate = (date: Date) => {
  const year = date.getFullYear();
  const month = pad(date.getMonth() + 1);
  const day = pad(date.getDate());

  return `${year}-${month}-${day}`;
};

export const useCheckAvailability = (
  meta: MaybeRefOrGetter<CheckAvailabilityMeta | null | undefined>,
) => {
  const today = new Date();
  const startDate = ref(toIsoDate(today));
  const endDate = ref(toIsoDate(addDays(today, 1)));
  const adults = ref(1);
  const children = ref(0);
  const rooms = ref(1);

  const resolvedMeta = computed(() => toValue(meta) || {});
  const actionUrl = computed(() => resolvedMeta.value.action_url || "");
  const dateFormat = computed(() => {
    const candidate = resolvedMeta.value.date_format || "d-m-Y";
    return BOTBLE_DATE_FORMATS.includes(candidate as (typeof BOTBLE_DATE_FORMATS)[number])
      ? candidate
      : "d-m-Y";
  });
  const minAdults = computed(() => Math.max(1, resolvedMeta.value.minimum_adults || 1));
  const maxAdults = computed(() => Math.max(minAdults.value, resolvedMeta.value.maximum_adults || minAdults.value));

  watchEffect(() => {
    adults.value = Math.min(maxAdults.value, Math.max(minAdults.value, adults.value || minAdults.value));
    children.value = Math.max(0, children.value || resolvedMeta.value.default_children || 0);
    rooms.value = Math.max(1, rooms.value || resolvedMeta.value.default_rooms || 1);

    if (endDate.value <= startDate.value) {
      const next = new Date(`${startDate.value}T00:00:00`);
      endDate.value = toIsoDate(addDays(next, 1));
    }
  });

  const submit = async () => {
    if (!actionUrl.value) {
      return;
    }

    const url = new URL(actionUrl.value);
    url.searchParams.set("start_date", formatBotbleDate(startDate.value, dateFormat.value));
    url.searchParams.set("end_date", formatBotbleDate(endDate.value, dateFormat.value));
    url.searchParams.set("adults", String(adults.value));
    url.searchParams.set("children", String(children.value));
    url.searchParams.set("rooms", String(rooms.value));

    await navigateTo(url.toString(), { external: true });
  };

  return {
    startDate,
    endDate,
    adults,
    children,
    rooms,
    minAdults,
    maxAdults,
    submit,
  };
};
