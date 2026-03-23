module.exports = {
    apps: [{
        name: "shopx-php",
        script: "php",
        args: "-S localhost:8000 -t project",
        exec_mode: "fork",
        instances: 1,
        autorestart: true,
        watch: ["project"],
        ignore_watch: [".git", "database/*.sql"],
        max_memory_restart: "1G",
        env: {
            PHP_ENV: "development"
        }
    }]
};
