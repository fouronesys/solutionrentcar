const appJson = require("./app.json");
const { withGradleProperties } = require("@expo/config-plugins");

// Config plugin to increase JVM memory for Gradle (prevents OOM on CI with Reanimated 4 / CMake)
function withJvmArgs(config) {
  return withGradleProperties(config, (cfg) => {
    const props = cfg.modResults;
    const set = (key, val) => {
      const i = props.findIndex((p) => p.type === "property" && p.key === key);
      if (i >= 0) props[i].value = val;
      else props.push({ type: "property", key, value: val });
    };
    set("org.gradle.jvmargs", "-Xmx4g -XX:MaxMetaspaceSize=1g -XX:+HeapDumpOnOutOfMemoryError -Dfile.encoding=UTF-8");
    set("org.gradle.daemon", "true");
    set("org.gradle.parallel", "true");
    set("org.gradle.configureondemand", "true");
    set("org.gradle.daemon.performance.disable-logging", "true");
    return cfg;
  });
}

module.exports = () => {
  const expo = appJson.expo;
  const googleServicesFile =
    process.env.GOOGLE_SERVICES_JSON || expo.android?.googleServicesFile || "./google-services.json";

  let config = {
    ...expo,
    android: {
      ...expo.android,
      googleServicesFile,
    },
  };

  config = withJvmArgs(config);

  return config;
};
