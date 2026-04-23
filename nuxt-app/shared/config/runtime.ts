import { DEFAULT_DEV_HOST, DEFAULT_DEV_PORT } from "./defaults";
import { normalizeSiteUrl } from "../utils/url";

export const buildDevOrigin = (
  host: string = DEFAULT_DEV_HOST,
  port: number | string = DEFAULT_DEV_PORT,
) => `http://${host}:${Number(port || DEFAULT_DEV_PORT)}`;

export const resolveCmsProxyBaseUrl = (options?: {
  explicitBaseUrl?: string | null;
  isDevelopment?: boolean;
  devHost?: string;
  devPort?: number | string;
}) => {
  const explicitBaseUrl = normalizeSiteUrl(options?.explicitBaseUrl);

  if (explicitBaseUrl) {
    return explicitBaseUrl;
  }

  if (options?.isDevelopment) {
    return buildDevOrigin(options?.devHost || DEFAULT_DEV_HOST, options?.devPort || DEFAULT_DEV_PORT);
  }

  return "";
};
