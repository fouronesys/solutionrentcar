import React, { useEffect } from "react";
import { Stack, useRouter, useSegments } from "expo-router";
import { StatusBar } from "expo-status-bar";
import * as Updates from "expo-updates";
import * as SplashScreen from "expo-splash-screen";
import { GestureHandlerRootView } from "react-native-gesture-handler";
import { SafeAreaProvider } from "react-native-safe-area-context";
import { AuthProvider, useAuth } from "@/auth/AuthContext";
import { NotificationsProvider } from "@/notifications/NotificationsContext";
import "@/i18n";

SplashScreen.preventAutoHideAsync().catch(() => {});

function Gate() {
  const { bootstrapped, role } = useAuth();
  const segments = useSegments();
  const router = useRouter();

  useEffect(() => {
    if (!bootstrapped) return;
    SplashScreen.hideAsync().catch(() => {});

    const first = segments[0] as string | undefined;
    const inClient = first === "(client)";
    const inStaff = first === "(staff)";
    const inAuth = first === "login" || first === "register";

    if (role === "client" && !inClient) {
      router.replace("/(client)/cars");
    } else if (role === "staff" && !inStaff) {
      router.replace("/(staff)/agenda");
    } else if (!role && inStaff) {
      router.replace("/login/staff");
    } else if (!role && !inClient && !inAuth) {
      router.replace("/(client)/cars");
    }
  }, [bootstrapped, role, segments, router]);

  return (
    <Stack screenOptions={{ headerShown: false, contentStyle: { backgroundColor: "#FFFFFF" } }} />
  );
}

function UpdatesWatcher() {
  useEffect(() => {
    (async () => {
      try {
        if (!Updates.isEnabled) return;
        const u = await Updates.checkForUpdateAsync();
        if (u.isAvailable) {
          await Updates.fetchUpdateAsync();
          await Updates.reloadAsync();
        }
      } catch {
        /* OTA failures are non-fatal */
      }
    })();
  }, []);
  return null;
}

export default function RootLayout() {
  return (
    <GestureHandlerRootView style={{ flex: 1 }}>
      <SafeAreaProvider>
        <AuthProvider>
          <NotificationsProvider>
            <UpdatesWatcher />
            <StatusBar style="dark" />
            <Gate />
          </NotificationsProvider>
        </AuthProvider>
      </SafeAreaProvider>
    </GestureHandlerRootView>
  );
}
