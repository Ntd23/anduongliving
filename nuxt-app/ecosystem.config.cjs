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
      env_file: ".env.production",
      env_production: {
        NODE_ENV: "production",
        NODE_OPTIONS: process.env.NODE_OPTIONS || "",
      },
    },
  ],
};
