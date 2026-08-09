import React from "react";
import { Platform, StyleSheet, Text, View } from "react-native";
import { Tabs } from "expo-router";
import { StatusBar } from "expo-status-bar";
import { Ionicons } from "@expo/vector-icons";
import { colors, font } from "@/theme/colors";
import { useNotificationsCtx } from "@/notifications/NotificationsContext";
import { t } from "@/i18n";

function TabIcon({
  name,
  focused,
}: {
  name: keyof typeof Ionicons.glyphMap;
  focused: boolean;
}) {
  return (
    <View style={styles.tabItem}>
      <Ionicons
        name={name}
        size={24}
        color={focused ? colors.cta : colors.textMuted}
      />
    </View>
  );
}

function BellTabIcon({ focused }: { focused: boolean }) {
  const { unread } = useNotificationsCtx();
  return (
    <View style={styles.tabItem}>
      <Ionicons
        name={focused ? "notifications" : "notifications-outline"}
        size={24}
        color={focused ? colors.cta : colors.textMuted}
      />
      {unread > 0 && (
        <View style={styles.badge}>
          <Text style={styles.badgeText}>{unread > 9 ? "9+" : unread}</Text>
        </View>
      )}
    </View>
  );
}

export default function ClientLayout() {
  return (
    <>
      <StatusBar style="dark" />
      <Tabs
        screenOptions={{
          headerShown: false,
          tabBarStyle: styles.tabBar,
          tabBarShowLabel: true,
          tabBarLabelStyle: styles.tabLabel,
          tabBarActiveTintColor: colors.cta,
          tabBarInactiveTintColor: colors.textMuted,
          tabBarItemStyle: { paddingTop: 6 },
        }}
      >
        {/* Inicio */}
        <Tabs.Screen
          name="index"
          options={{
            title: "Inicio",
            tabBarIcon: ({ focused }) => (
              <TabIcon name={focused ? "home" : "home-outline"} focused={focused} />
            ),
          }}
        />

        {/* Autos */}
        <Tabs.Screen
          name="cars"
          options={{
            title: t("tabs.cars"),
            tabBarIcon: ({ focused }) => (
              <TabIcon name={focused ? "car-sport" : "car-sport-outline"} focused={focused} />
            ),
          }}
        />

        {/* Ubicaciones */}
        <Tabs.Screen
          name="locations"
          options={{
            title: "Ubicaciones",
            tabBarIcon: ({ focused }) => (
              <TabIcon name={focused ? "location" : "location-outline"} focused={focused} />
            ),
          }}
        />

        {/* Perfil */}
        <Tabs.Screen
          name="profile"
          options={{
            title: t("tabs.profile"),
            tabBarIcon: ({ focused }) => (
              <TabIcon name={focused ? "person" : "person-outline"} focused={focused} />
            ),
          }}
        />

        {/* Hidden routes */}
        <Tabs.Screen name="bookings" options={{ href: null }} />
        <Tabs.Screen name="notifications" options={{ href: null }} />
        <Tabs.Screen name="car/[id]" options={{ href: null }} />
        <Tabs.Screen name="book/[carId]" options={{ href: null }} />
        <Tabs.Screen name="booking/[id]" options={{ href: null }} />
        <Tabs.Screen name="sign/[id]" options={{ href: null }} />
      </Tabs>
    </>
  );
}

const styles = StyleSheet.create({
  tabBar: {
    backgroundColor: colors.card,
    borderTopWidth: 1,
    borderTopColor: colors.border,
    height: Platform.OS === "web" ? 84 : 88,
    paddingBottom: Platform.OS === "web" ? 34 : 28,
    paddingTop: 6,
    elevation: 0,
    shadowOpacity: 0,
  },
  tabLabel: { fontFamily: font.semibold, fontSize: 11, marginTop: 2 },
  tabItem: { alignItems: "center", justifyContent: "center", width: 48, height: 30 },
  badge: {
    position: "absolute",
    top: -5,
    right: 4,
    minWidth: 18,
    height: 18,
    borderRadius: 9,
    backgroundColor: colors.cta,
    alignItems: "center",
    justifyContent: "center",
    paddingHorizontal: 4,
    borderWidth: 2,
    borderColor: colors.card,
  },
  badgeText: { color: "#fff", fontSize: 9, fontFamily: font.bold },
});
