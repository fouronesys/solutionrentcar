import React, { useEffect, useState } from "react";
import { Stack, useRouter, useSegments } from "expo-router";
import { StatusBar } from "expo-status-bar";
import * as Updates from "expo-updates";
import * as SplashScreen from "expo-splash-screen";
import { GestureHandlerRootView } from "react-native-gesture-handler";
import { SafeAreaProvider } from "react-native-safe-area-context";
import {
  useFonts,
  Inter_400Regular,
  Inter_500Medium,
  Inter_600SemiBold,
  Inter_700Bold,
  Inter_800ExtraBold,
} from "@expo-google-fonts/inter";
import { Ionicons } from "@expo/vector-icons";
import { AuthProvider, useAuth } from "@/auth/AuthContext";
import { NotificationsProvider } from "@/notifications/NotificationsContext";
import { colors } from "@/theme/colors";
import AnimatedSplash from "@/components/AnimatedSplash";
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
    } else if (!role) {
      // Logged-out users browse the catalog as guests; only the staff area and
      // explicit auth screens are excluded. Login is requested at reservation.
      if (inStaff) {
        router.replace("/login/staff");
      } else if (!inClient && !inAuth) {
        router.replace("/(client)/cars");
      }
    }
  }, [bootstrapped, role, segments, router]);

  return (
    <Stack screenOptions={{ headerShown: false, contentStyle: { backgroundColor: colors.bg } }} />
  );
}

function SplashOverlay() {
  const { bootstrapped } = useAuth();
  const [done, setDone] = useState(false);
  if (done) return null;
  return <AnimatedSplash appReady={bootstrapped} onFinish={() => setDone(true)} />;
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
  const [fontsLoaded, fontError] = useFonts({
    ...Ionicons.font,
    Inter_400Regular,
    Inter_500Medium,
    Inter_600SemiBold,
    Inter_700Bold,
    Inter_800ExtraBold,
  });

  useEffect(() => {
    if (fontsLoaded || fontError) {
      SplashScreen.hideAsync().catch(() => {});
    }
  }, [fontsLoaded, fontError]);

  if (!fontsLoaded && !fontError) return null;

  return (
    <GestureHandlerRootView style={{ flex: 1 }}>
      <SafeAreaProvider>
        <AuthProvider>
          <NotificationsProvider>
            <UpdatesWatcher />
            <StatusBar style="light" />
            <Gate />
            <SplashOverlay />
          </NotificationsProvider>
        </AuthProvider>
      </SafeAreaProvider>
    </GestureHandlerRootView>
  );
}
