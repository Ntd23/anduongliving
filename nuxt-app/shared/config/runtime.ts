import { DEFAULT_DEV_HOST, DEFAULT_DEV_PORT } from "./defaults";
import { normalizeSiteUrl } from "../utils/url";

export const buildDevOrigin = (
  host: string = DEFAULT_DEV_HOST,
  port: number | string = DEFAULT_DEV_PORT,
) => `http://${host}:${Number(port || DEFAULT_DEV_PORT)}`;

const isPrivateOrLoopbackHost = (host: string) => {
  const normalizedHost = host.trim().toLowerCase();

  if (!normalizedHost) {
    return false;
  }

  if (
    normalizedHost === "localhost" ||
    normalizedHost === "127.0.0.1" ||
    normalizedHost === "0.0.0.0" ||
    normalizedHost === "::1"
  ) {
    return true;
  }

  if (/^10\.\d+\.\d+\.\d+$/.test(normalizedHost)) {
    return true;
  }

  if (/^192\.168\.\d+\.\d+$/.test(normalizedHost)) {
    return true;
  }

  const private172 = /^172\.(\d+)\.\d+\.\d+$/.exec(normalizedHost);

  if (private172) {
    const secondOctet = Number.parseInt(private172[1], 10);

    if (secondOctet >= 16 && secondOctet <= 31) {
      return true;
    }
  }

  return false;
};

const isUnsafePublicProxyBaseUrl = (value: string) => {
  try {
    const parsed = new URL(value);

    return isPrivateOrLoopbackHost(parsed.hostname);
  } catch {
    return false;
  }
};

export const resolveCmsProxyBaseUrl = (options?: {
  explicitBaseUrl?: string | null;
  isDevelopment?: boolean;
  devHost?: string;
  devPort?: number | string;
  publicSiteUrl?: string | null;
}) => {
  const explicitBaseUrl = normalizeSiteUrl(options?.explicitBaseUrl);
  const publicSiteUrl = normalizeSiteUrl(options?.publicSiteUrl);

  if (explicitBaseUrl) {
    if (!options?.isDevelopment && isUnsafePublicProxyBaseUrl(explicitBaseUrl)) {
      return publicSiteUrl || "";
    }

    return explicitBaseUrl;
  }

  if (options?.isDevelopment) {
    return buildDevOrigin(options?.devHost || DEFAULT_DEV_HOST, options?.devPort || DEFAULT_DEV_PORT);
  }

  return publicSiteUrl || "";
};
