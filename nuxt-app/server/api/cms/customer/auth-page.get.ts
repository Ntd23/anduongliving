import { Buffer } from "node:buffer";
import type {
  CustomerAuthActions,
  CustomerAuthAgreement,
  CustomerAuthCard,
  CustomerAuthField,
  CustomerAuthMode,
  CustomerAuthPageData,
} from "~~/shared/customer-auth";
import { cmsAppRoutes } from "~~/shared/routes/app";
import { buildAbsoluteUrl, normalizeSiteUrl } from "~~/shared/utils/url";
import { createForwardHeaders, forwardUpstreamCookies } from "~~/server/features/http/upstream";

const resolveMode = (value?: string | null): CustomerAuthMode =>
  value === "register" ? "register" : "login";

const DEFAULT_LABELS: Record<CustomerAuthMode, string> = {
  login: "Login",
  register: "Register",
};

const DEFAULT_CARD_HEADINGS: Record<CustomerAuthMode, string> = {
  login: "Sign in to your account",
  register: "Create your account",
};

const DEFAULT_SUBMIT_LABELS: Record<CustomerAuthMode, string> = {
  login: "Login",
  register: "Register",
};

const DEFAULT_ALT_LABELS: Record<CustomerAuthMode, { prompt: string; action: string; href: string }> = {
  login: {
    prompt: "Don't have an account?",
    action: "Register now",
    href: cmsAppRoutes.auth.register(),
  },
  register: {
    prompt: "Already have an account?",
    action: "Login",
    href: cmsAppRoutes.auth.login(),
  },
};

const escapeRegExp = (value: string) => value.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");

const decodeHtmlEntities = (value?: string | null) =>
  String(value || "")
    .replace(/&nbsp;/gi, " ")
    .replace(/&amp;/gi, "&")
    .replace(/&quot;/gi, "\"")
    .replace(/&#039;|&#39;/gi, "'")
    .replace(/&lt;/gi, "<")
    .replace(/&gt;/gi, ">");

const repairMojibake = (value: string) => {
  if (!/[ÃÐÂ]/.test(value)) {
    return value;
  }

  try {
    return Buffer.from(value, "latin1").toString("utf8");
  } catch {
    return value;
  }
};

const normalizeText = (value?: string | null) =>
  repairMojibake(
    decodeHtmlEntities(String(value || "").replace(/<[^>]+>/g, " ").replace(/\s+/g, " ")),
  ).trim();

const decodeAttribute = (value?: string | null) =>
  repairMojibake(decodeHtmlEntities(value).trim());

const readHtml = async (response: Response) => {
  const buffer = await response.arrayBuffer();

  return new TextDecoder("utf-8").decode(buffer);
};

const extractAttribute = (tag: string, name: string) =>
  tag.match(new RegExp(`${escapeRegExp(name)}=(["'])(.*?)\\1`, "i"))?.[2] || "";

const extractInputTags = (html: string) =>
  Array.from(html.matchAll(/<input\b([^>]*)>/gi)).map((match) => `<input${match[1] || ""}>`);

const extractInputValue = (html: string, fieldName: string) => {
  for (const tag of extractInputTags(html)) {
    if (extractAttribute(tag, "name") !== fieldName) {
      continue;
    }

    return decodeAttribute(extractAttribute(tag, "value")) || "";
  }

  return "";
};

const extractMetaContent = (html: string, metaName: string) =>
  html.match(
    new RegExp(`<meta\\b[^>]*name=(["'])${escapeRegExp(metaName)}\\1[^>]*content=(["'])(.*?)\\2[^>]*>`, "i"),
  )?.[3] || "";

const collectForms = (html: string) =>
  Array.from(html.matchAll(/<form\b[\s\S]*?<\/form>/gi)).map((match) => match[0]);

const scoreAuthForm = (formHtml: string, mode: CustomerAuthMode) => {
  const formTag = formHtml.match(/<form\b[^>]*>/i)?.[0] || "";
  const action = decodeAttribute(extractAttribute(formTag, "action")).toLowerCase();
  const inputNames = new Set(
    extractInputTags(formHtml)
      .map((tag) => extractAttribute(tag, "name"))
      .filter((value): value is string => Boolean(value))
      .map((value) => value.toLowerCase()),
  );

  let score = 0;

  if (action.includes(mode)) {
    score += 8;
  }

  if (inputNames.has("_token")) {
    score += 4;
  }

  if (inputNames.has("email")) {
    score += 2;
  }

  if (inputNames.has("password")) {
    score += 2;
  }

  if (mode === "register") {
    if (inputNames.has("first_name")) {
      score += 4;
    }

    if (inputNames.has("password_confirmation")) {
      score += 4;
    }
  }

  if (mode === "login" && inputNames.has("remember")) {
    score += 2;
  }

  return score;
};

const extractFormHtml = (html: string, mode: CustomerAuthMode) => {
  const forms = collectForms(html);

  if (!forms.length) {
    return "";
  }

  return forms
    .map((formHtml) => ({
      formHtml,
      score: scoreAuthForm(formHtml, mode),
    }))
    .sort((left, right) => right.score - left.score)[0]?.formHtml || forms[0];
};

const hasBooleanAttribute = (tag: string, name: string) =>
  new RegExp(`\\b${escapeRegExp(name)}(?:=(["']).*?\\1)?(?=[\\s>])`, "i").test(tag);

const deriveIcon = (name: string, type: string) => {
  const normalized = `${name} ${type}`.toLowerCase();

  if (normalized.includes("password")) {
    return "lock";
  }

  if (normalized.includes("email")) {
    return "mail";
  }

  if (normalized.includes("name")) {
    return "user";
  }

  if (normalized.includes("phone") || normalized.includes("tel")) {
    return "phone";
  }

  return null;
};

const normalizeCardIcon = (value?: string | null) => {
  const source = String(value || "").toLowerCase();

  return source.match(/(?:ti-ti-|ti ti-)([a-z0-9-]+)/)?.[1] ||
    source.match(/svg-icon-ti-ti-([a-z0-9-]+)/)?.[1] ||
    null;
};

const resolveFieldLabel = (formHtml: string, name: string) => {
  const labelMatch = formHtml.match(
    new RegExp(`<label\\b[^>]*for=(["'])${escapeRegExp(name)}\\1[^>]*>([\\s\\S]*?)<\\/label>`, "i"),
  );

  return {
    text: normalizeText(labelMatch?.[2]),
    required: /\brequired\b/i.test(labelMatch?.[0] || ""),
  };
};

const extractFields = (formHtml: string): CustomerAuthField[] => {
  const fields: CustomerAuthField[] = [];
  const fieldMatches = formHtml.matchAll(/<(input|textarea|select)\b([^>]*)>/gi);

  for (const match of fieldMatches) {
    const tagName = match[1].toLowerCase();
    const rawAttributes = match[2] || "";
    const fullTag = `<${tagName}${rawAttributes}>`;
    const name = extractAttribute(fullTag, "name");
    const type = (extractAttribute(fullTag, "type") || tagName).toLowerCase();

    if (!name || name === "_token" || type === "hidden" || type === "checkbox" || type === "submit" || type === "button") {
      continue;
    }

    const label = resolveFieldLabel(formHtml, name);
    fields.push({
      name,
      type: type === "email" ? "email" : type === "password" ? "password" : type === "tel" ? "tel" : "text",
      label: label.text || name,
      placeholder: decodeAttribute(extractAttribute(fullTag, "placeholder")) || null,
      required: hasBooleanAttribute(fullTag, "required") || label.required,
      autocomplete: decodeAttribute(extractAttribute(fullTag, "autocomplete")) || null,
      visible: true,
      icon: deriveIcon(name, type),
    });
  }

  return fields;
};

const extractAgreements = (formHtml: string): CustomerAuthAgreement[] =>
  Array.from(
    formHtml.matchAll(
      /<label\b[^>]*class=(["'])[^"']*form-check[^"']*\1[\s\S]*?<input\b[^>]*type=(["'])checkbox\2[^>]*name=(["'])(.*?)\3[\s\S]*?<span\b[^>]*class=(["'])[^"']*form-check-label[^"']*\5[^>]*>([\s\S]*?)<\/span>[\s\S]*?<\/label>/gi,
    ),
  ).map((match) => {
    const name = match[4];
    const label = normalizeText(match[6]);
    const normalized = name.toLowerCase();

    return {
      name,
      label,
      required: /agree|terms|policy/.test(normalized),
      checked: false,
      kind: /remember/.test(normalized) ? "remember" : /agree|terms|policy/.test(normalized) ? "terms" : "checkbox",
    };
  });

const extractActions = (formHtml: string, mode: CustomerAuthMode, backendSiteUrl: string): CustomerAuthActions => {
  const submitLabel =
    normalizeText(formHtml.match(/<button\b[^>]*type=(["'])submit\1[^>]*>([\s\S]*?)<\/button>/i)?.[2]) ||
    DEFAULT_SUBMIT_LABELS[mode];

  const forgotMatch = formHtml.match(/<a\b[^>]*href=(["'])(.*?\/password\/request.*?)\1[^>]*>([\s\S]*?)<\/a>/i);
  const alternateMatch = formHtml.match(/<div\b[^>]*class=(["'])[^"']*text-center[^"']*\1[^>]*>([\s\S]*?)<\/div>/i);
  const alternateAnchor = alternateMatch?.[2].match(/<a\b[^>]*href=(["'])(.*?)\1[^>]*>([\s\S]*?)<\/a>/i);

  const fallbackAlternate = DEFAULT_ALT_LABELS[mode];
  const alternateUrl = decodeAttribute(alternateAnchor?.[2]) || fallbackAlternate.href;

  return {
    submitLabel,
    alternatePrompt:
      normalizeText((alternateMatch?.[2] || "").replace(/<a\b[\s\S]*?<\/a>/i, "")) ||
      fallbackAlternate.prompt,
    alternateLabel: normalizeText(alternateAnchor?.[3]) || fallbackAlternate.action,
    alternateUrl:
      alternateUrl.includes("/login")
        ? cmsAppRoutes.auth.login()
        : alternateUrl.includes("/register")
          ? cmsAppRoutes.auth.register()
          : buildAbsoluteUrl(backendSiteUrl, alternateUrl),
    forgotPasswordLabel: normalizeText(forgotMatch?.[3]) || null,
    forgotPasswordUrl: forgotMatch?.[2] ? buildAbsoluteUrl(backendSiteUrl, decodeAttribute(forgotMatch[2])) : null,
  };
};

const extractHero = (html: string, mode: CustomerAuthMode, backendSiteUrl: string) => {
  const title = normalizeText(html.match(/<section\b[^>]*class=(["'])[^"']*breadcrumb-area[^"']*\1[\s\S]*?<h2>([\s\S]*?)<\/h2>/i)?.[2]) ||
    DEFAULT_LABELS[mode];
  const breadcrumbAnchor = html.match(
    /<li\b[^>]*class=(["'])[^"']*breadcrumb-item[^"']*\1[^>]*>\s*<a\b[^>]*href=(["'])(.*?)\2[^>]*>([\s\S]*?)<\/a>/i,
  );
  const backgroundImage =
    decodeAttribute(html.match(/breadcrumb-area[^>]*style=(["'])(.*?)\1/i)?.[2]).match(/url\((['"]?)(.*?)\1\)/i)?.[2] ||
    "";

  return {
    title,
    breadcrumbLabel: title,
    homeLabel: normalizeText(breadcrumbAnchor?.[4]) || "Home",
    homeUrl: breadcrumbAnchor?.[3] ? buildAbsoluteUrl(backendSiteUrl, decodeAttribute(breadcrumbAnchor[3])) : "/",
    backgroundImageUrl: backgroundImage ? buildAbsoluteUrl(backendSiteUrl, backgroundImage) : null,
  };
};

const extractCard = (html: string, formHtml: string, mode: CustomerAuthMode): CustomerAuthCard => {
  const formTag = formHtml.match(/<form\b[^>]*>/i)?.[0] || "";
  const heading =
    decodeAttribute(extractAttribute(formTag, "heading")) ||
    normalizeText(html.match(/<div\b[^>]*class=(["'])[^"']*card-header[^"']*\1[\s\S]*?<h3[^>]*>([\s\S]*?)<\/h3>/i)?.[2]) ||
    normalizeText(html.match(/<div\b[^>]*class=(["'])[^"']*about-title[^"']*\1[\s\S]*?<h1[^>]*>([\s\S]*?)<\/h1>/i)?.[2]) ||
    DEFAULT_CARD_HEADINGS[mode];
  const description =
    decodeAttribute(extractAttribute(formTag, "description")) ||
    normalizeText(html.match(/<div\b[^>]*class=(["'])[^"']*card-header[^"']*\1[\s\S]*?<p[^>]*>([\s\S]*?)<\/p>/i)?.[2]) ||
    null;
  const iconSource =
    extractAttribute(formTag, "icon") ||
    html.match(/<div\b[^>]*class=(["'])[^"']*card-header[^"']*\1[\s\S]*?<svg\b[^>]*class=(["'])(.*?)\2/i)?.[3] ||
    "";

  return {
    icon: normalizeCardIcon(iconSource) || (mode === "register" ? "user-plus" : "lock"),
    heading,
    description,
  };
};

export default defineEventHandler(async (event) => {
  const config = useRuntimeConfig(event);
  const mode = resolveMode(getQuery(event).mode as string | undefined);
  const backendSiteUrl = normalizeSiteUrl(config.backendSiteUrl);

  if (!backendSiteUrl) {
    throw createError({
      statusCode: 503,
      statusMessage: "Auth backend is not configured",
    });
  }

  const targetUrl = buildAbsoluteUrl(
    backendSiteUrl,
    mode === "register" ? cmsAppRoutes.auth.register() : cmsAppRoutes.auth.login(),
  );
  const response = await fetch(targetUrl, {
    headers: createForwardHeaders(event, "text/html,application/xhtml+xml"),
  });

  forwardUpstreamCookies(event, response);

  if (!response.ok) {
    throw createError({
      statusCode: response.status || 500,
      statusMessage: response.statusText || "Failed to resolve auth page",
    });
  }

  const html = await readHtml(response);
  const formHtml = extractFormHtml(html, mode);
  const csrfToken =
    extractInputValue(formHtml, "_token") ||
    extractInputValue(html, "_token") ||
    decodeAttribute(extractMetaContent(html, "csrf-token")) ||
    "";

  if (!formHtml || !csrfToken) {
    throw createError({
      statusCode: 500,
      statusMessage: "Failed to resolve authentication content",
    });
  }

  const data: CustomerAuthPageData = {
    mode,
    csrfToken,
    hero: extractHero(html, mode, backendSiteUrl),
    card: extractCard(html, formHtml, mode),
    fields: extractFields(formHtml),
    agreements: extractAgreements(formHtml),
    actions: extractActions(formHtml, mode, backendSiteUrl),
  };

  return { data };
});
