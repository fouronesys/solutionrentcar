import * as Notifications from "expo-notifications";
import * as Device from "expo-device";
import Constants from "expo-constants";
import { Platform } from "react-native";
import * as Application from "expo-application";
import { api } from "@/api/client";

Notifications.setNotificationHandler({
  handleNotification: async () => ({
    shouldShowAlert: true,
    shouldShowBanner: true,
    shouldShowList: true,
    shouldPlaySound: true,
    shouldSetBadge: true,
  }),
});

export async function ensureAndroidChannel() {
  if (Platform.OS === "android") {
    await Notifications.setNotificationChannelAsync("default", {
      name: "Casa Rivas RentCar",
      importance: Notifications.AndroidImportance.HIGH,
      vibrationPattern: [0, 250, 250, 250],
      lightColor: "#C79323",
    });
  }
}

export async function registerForPush(): Promise<string | null> {
  if (!Device.isDevice) return null;
  // Remote push tokens removed from Expo Go in SDK 53 (all platforms)
  if (Constants.appOwnership === "expo") return null;
  await ensureAndroidChannel();
  const { status: existing } = await Notifications.getPermissionsAsync();
  let final = existing;
  if (existing !== "granted") {
    const req = await Notifications.requestPermissionsAsync();
    final = req.status;
  }
  if (final !== "granted") return null;

  const projectId =
    (Constants.expoConfig?.extra as { eas?: { projectId?: string } } | undefined)?.eas?.projectId ??
    Constants.easConfig?.projectId;

  const token = (
    await Notifications.getExpoPushTokenAsync(projectId ? { projectId } : undefined)
  ).data;
  return token;
}

export async function registerTokenWithServer(token: string) {
  try {
    await api.post("/push/register", {
      token,
      platform: Platform.OS,
      app_version: Application.nativeApplicationVersion ?? "1.0.0",
      device_info: `${Device.modelName ?? "device"} ${Device.osName ?? ""} ${Device.osVersion ?? ""}`.trim(),
    });
  } catch {
    /* swallow */
  }
}

export async function unregisterTokenFromServer(token: string) {
  try {
    await api.del("/push/token", { token });
  } catch {
    /* ignore */
  }
}
