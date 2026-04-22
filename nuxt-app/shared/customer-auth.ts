export type CustomerAuthMode = "login" | "register";

export type CustomerAuthHero = {
  title: string;
  breadcrumbLabel: string;
  homeLabel: string | null;
  homeUrl: string | null;
  backgroundImageUrl: string | null;
};

export type CustomerAuthCard = {
  icon: string | null;
  heading: string;
  description: string | null;
};

export type CustomerAuthField = {
  name: string;
  type: "text" | "email" | "password" | "tel";
  label: string;
  placeholder: string | null;
  required: boolean;
  autocomplete: string | null;
  visible: boolean;
  icon: string | null;
};

export type CustomerAuthAgreement = {
  name: string;
  label: string;
  required: boolean;
  checked: boolean;
  kind: "remember" | "terms" | "checkbox";
};

export type CustomerAuthActions = {
  submitLabel: string;
  alternatePrompt: string | null;
  alternateLabel: string | null;
  alternateUrl: string | null;
  forgotPasswordLabel: string | null;
  forgotPasswordUrl: string | null;
};

export type CustomerAuthPageData = {
  mode: CustomerAuthMode;
  csrfToken: string;
  hero: CustomerAuthHero;
  card: CustomerAuthCard;
  fields: CustomerAuthField[];
  agreements: CustomerAuthAgreement[];
  actions: CustomerAuthActions;
};
