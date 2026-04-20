type SupportedLocale = "vi" | "en" | "ja";
type MonthStyle = "short" | "long";

const EN_MONTHS_SHORT = [
  "Jan",
  "Feb",
  "Mar",
  "Apr",
  "May",
  "Jun",
  "Jul",
  "Aug",
  "Sep",
  "Oct",
  "Nov",
  "Dec",
];

const EN_MONTHS_LONG = [
  "January",
  "February",
  "March",
  "April",
  "May",
  "June",
  "July",
  "August",
  "September",
  "October",
  "November",
  "December",
];

const VI_MONTHS_SHORT = [
  "thg 1",
  "thg 2",
  "thg 3",
  "thg 4",
  "thg 5",
  "thg 6",
  "thg 7",
  "thg 8",
  "thg 9",
  "thg 10",
  "thg 11",
  "thg 12",
];

const VI_MONTHS_LONG = [
  "thang 1",
  "thang 2",
  "thang 3",
  "thang 4",
  "thang 5",
  "thang 6",
  "thang 7",
  "thang 8",
  "thang 9",
  "thang 10",
  "thang 11",
  "thang 12",
];

const pad2 = (value: number) => String(value).padStart(2, "0");

export const normalizeUiLocale = (value?: string | null): SupportedLocale => {
  const normalized = String(value || "").toLowerCase();

  if (normalized.startsWith("en")) {
    return "en";
  }

  if (normalized.startsWith("ja")) {
    return "ja";
  }

  return "vi";
};

export const formatCmsInteger = (
  value: number,
  locale?: string | null,
) => {
  const normalized = normalizeUiLocale(locale);
  const separator = normalized === "vi" ? "." : ",";

  return Math.max(0, Math.trunc(value))
    .toString()
    .replace(/\B(?=(\d{3})+(?!\d))/g, separator);
};

export const formatCmsDate = (
  input?: string | null,
  locale?: string | null,
  options?: {
    monthStyle?: MonthStyle;
  },
) => {
  if (!input) {
    return null;
  }

  const date = new Date(input);

  if (Number.isNaN(date.getTime())) {
    return null;
  }

  const monthStyle = options?.monthStyle || "short";
  const normalized = normalizeUiLocale(locale);
  const day = date.getUTCDate();
  const monthIndex = date.getUTCMonth();
  const month = monthIndex + 1;
  const year = date.getUTCFullYear();

  if (normalized === "ja") {
    return `${year}/${pad2(month)}/${pad2(day)}`;
  }

  if (normalized === "en") {
    const label = monthStyle === "long" ? EN_MONTHS_LONG[monthIndex] : EN_MONTHS_SHORT[monthIndex];
    return `${label} ${day}, ${year}`;
  }

  const label = monthStyle === "long" ? VI_MONTHS_LONG[monthIndex] : VI_MONTHS_SHORT[monthIndex];
  return `${day} ${label}, ${year}`;
};
