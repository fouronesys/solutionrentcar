import React from "react";
import { StyleSheet, Text, View } from "react-native";
import { Tabs } from "expo-router";
import { StatusBar } from "expo-status-bar";
import { colors, radius } from "@/theme/colors";
import { useNotificationsCtx } from "@/notifications/NotificationsContext";
import { t } from "@/i18n";

function TabIcon({ emoji, focused }: { emoji: string; focused: boolean }) {
  return (
    <View style={[styles.tabItem, focused && styles.tabItemActive]}>
      <Text style={[styles.tabEmoji, { opacity: focused ? 1 : 0.5 }]}>{emoji}</Text>
    </View>
  );
}

function InboxIcon({ focused }: { focused: boolean }) {
  const { unread } = useNotificationsCtx();
  return (
    <View style={[styles.tabItem, focused && styles.tabItemActive]}>
      <Text style={[styles.tabEmoji, { opacity: focused ? 1 : 0.5 }]}>🔔</Text>
      {unread > 0 && (
        <View style={styles.badge}>
          <Text style={styles.badgeText}>{unread > 9 ? "9+" : unread}</Text>
        </View>
      )}
    </View>
  );
}

export default function StaffLayout() {
  return (
    <>
      <StatusBar style="dark" />
      <Tabs
        screenOptions={{
          headerStyle: {
            backgroundColor: colors.dark,
            elevation: 0, shadowOpacity: 0,
          } as object,
          headerTintColor: "#fff",
          headerTitleStyle: { fontWeight: "700" as const, fontSize: 18 },
          tabBarStyle: styles.tabBar,
          tabBarLabelStyle: styles.tabLabel,
          tabBarActiveTintColor: colors.primaryDark,
          tabBarInactiveTintColor: colors.textMuted,
          tabBarIconStyle: { marginBottom: -2 },
        }}
      >
        <Tabs.Screen
          name="agenda"
          options={{
            title: t("tabs.agenda"),
            tabBarIcon: ({ focused }) => <TabIcon emoji="🗓️" focused={focused} />,
          }}
        />
        <Tabs.Screen
          name="bookings"
          options={{
            title: t("tabs.bookings"),
            tabBarIcon: ({ focused }) => <TabIcon emoji="📋" focused={focused} />,
          }}
        />
        <Tabs.Screen
          name="notifications"
          options={{
            title: t("tabs.notifications"),
            tabBarIcon: ({ focused }) => <InboxIcon focused={focused} />,
          }}
        />
        <Tabs.Screen
          name="profile"
          options={{
            title: t("tabs.profile"),
            tabBarIcon: ({ focused }) => <TabIcon emoji="👤" focused={focused} />,
          }}
        />
        <Tabs.Screen name="booking/[id]" options={{ href: null }} />
        <Tabs.Screen name="pay/[bookingId]" options={{ href: null }} />
      </Tabs>
    </>
  );
}

const styles = StyleSheet.create({
  tabBar: {
    backgroundColor: colors.card,
    borderTopWidth: 1, borderTopColor: colors.border,
    height: 64, paddingBottom: 8, paddingTop: 6,
    elevation: 0, shadowOpacity: 0,
  },
  tabLabel: { fontSize: 11, fontWeight: "600" },
  tabItem: {
    alignItems: "center", justifyContent: "center",
    width: 44, height: 34, borderRadius: radius.md,
  },
  tabItemActive: { backgroundColor: colors.primaryXLight },
  tabEmoji: { fontSize: 18 },
  badge: {
    position: "absolute", top: -3, right: -8,
    minWidth: 16, height: 16, borderRadius: 8,
    backgroundColor: colors.danger,
    alignItems: "center", justifyContent: "center",
    paddingHorizontal: 3,
    borderWidth: 1.5, borderColor: colors.card,
  },
  badgeText: { color: "#fff", fontSize: 9, fontWeight: "800" },
});
