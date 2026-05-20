const appJson = require("./app.json");

module.exports = () => {
  const expo = appJson.expo;
  const googleServicesFile =
    process.env.GOOGLE_SERVICES_JSON || expo.android?.googleServicesFile || "./google-services.json";

  return {
    ...expo,
    android: {
      ...expo.android,
      googleServicesFile,
    },
  };
};
