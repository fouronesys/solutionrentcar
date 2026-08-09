const { withGradleProperties } = require("@expo/config-plugins");

/**
 * Config plugin: aumenta la memoria JVM de Gradle para evitar OOM
 * en builds con Reanimated 4 / CMake (tanto en CI como localmente).
 */
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

module.exports = withJvmArgs;
