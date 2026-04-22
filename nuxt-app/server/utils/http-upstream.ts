import type { H3Event } from "h3";

const splitCombinedSetCookieHeader = (value: string) => {
  const cookies: string[] = [];
  let start = 0;
  let inExpires = false;

  for (let index = 0; index < value.length; index += 1) {
    const current = value[index];
    const tail = value.slice(index).toLowerCase();

    if (!inExpires && tail.startsWith("expires=")) {
      inExpires = true;
      index += "expires=".length - 1;
      continue;
    }

    if (inExpires && current === ";") {
      inExpires = false;
      continue;
    }

    if (inExpires || current !== ",") {
      continue;
    }

    const nextPart = value.slice(index + 1);

    if (!/^\s*[!#$%&'*+\-.^_`|~0-9A-Za-z]+\s*=/.test(nextPart)) {
      continue;
    }

    cookies.push(value.slice(start, index).trim());
    start = index + 1;
  }

  const lastCookie = value.slice(start).trim();

  if (lastCookie) {
    cookies.push(lastCookie);
  }

  return cookies;
};

const readSetCookieHeaders = (response: Response) => {
  const headers = response.headers as Headers & {
    getSetCookie?: () => string[];
  };

  if (typeof headers.getSetCookie === "function") {
    return headers.getSetCookie().filter(Boolean);
  }

  const singleHeader = response.headers.get("set-cookie");
  return singleHeader ? splitCombinedSetCookieHeader(singleHeader) : [];
};

export const forwardUpstreamCookies = (event: H3Event, response: Response) => {
  for (const value of readSetCookieHeaders(response)) {
    appendResponseHeader(event, "set-cookie", value);
  }
};

export const createForwardHeaders = (event: H3Event, accept: string) => ({
  Accept: accept,
  cookie: getHeader(event, "cookie") || "",
  "user-agent": getHeader(event, "user-agent") || "",
});
