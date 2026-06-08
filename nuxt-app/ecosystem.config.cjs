const fs = require("fs");
const path = require("path");

function loadEnvFile(filename) {
  if (!fs.existsSync(filename)) {
    return {};
  }

  const lines = fs.readFileSync(filename, "utf8").split(/\r?\n/);
  const env = {};

  for (const rawLine of lines) {
    const line = rawLine.trim();

    if (!line || line.startsWith("#")) {
      continue;
    }

    const separatorIndex = line.indexOf("=");

    if (separatorIndex === -1) {
      continue;
    }

    const key = line.slice(0, separatorIndex).trim();
    let value = line.slice(separatorIndex + 1).trim();

    if (
      (value.startsWith('"') && value.endsWith('"')) ||
      (value.startsWith("'") && value.endsWith("'"))
    ) {
      value = value.slice(1, -1);
    }

    env[key] = value;
  }

  return env;
}

const fileEnv = loadEnvFile(path.join(__dirname, ".env.production"));

module.exports = {
  apps: [
    {
      name: "anduongliving",
      cwd: __dirname,
      script: ".output/server/index.mjs",
      interpreter: "node",
      exec_mode: "fork",
      instances: 1,
      autorestart: true,
      max_memory_restart: "512M",
      watch: false,
      env: {
        ...fileEnv,
      },
      env_production: {
        ...fileEnv,
        NODE_ENV: "production",
        NODE_OPTIONS: process.env.NODE_OPTIONS || "",
      },
    },
  ],
};
