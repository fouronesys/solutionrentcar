import React from "react";
import { Tabs } from "expo-router";
import { Text, View } from "react-native";
import { colors } from "@/theme/colors";
import { useNotificationsCtx } from "@/notifications/NotificationsContext";
import { t } from "@/i18n";

function TabIcon({ label, focused }: { label: string; focused: boolean }) {
  return <Text style={{ fontSize: 20, opacity: focused ? 1 : 0.6 }}>{label}</Text>;
}

function InboxIcon({ focused }: { focused: boolean }) {
  const { unread } = useNotificationsCtx();
  return (
    <View>
      <Text style={{ fontSize: 20, opacity: focused ? 1 : 0.6 }}>🔔</Text>
      {unread > 0 && (
        <View
          style={{
            position: "absolute",
            top: -4,
            right: -10,
            minWidth: 16,
            height: 16,
            borderRadius: 8,
            backgroundColor: colors.danger,
            alignItems: "center",
            justifyContent: "center",
            paddingHorizontal: 4,
          }}
        >
          <Text style={{ color: "#fff", fontSize: 10, fontWeight: "700" }}>{unread > 9 ? "9+" : unread}</Text>
        </View>
      )}
    </View>
  );
}

export default function StaffLayout() {
  return (
    <Tabs
      screenOptions={{
        headerStyle: { backgroundColor: colors.primary },
        headerTintColor: "#fff",
        tabBarActiveTintColor: colors.primary,
      }}
    >
      <Tabs.Screen
        name="agenda"
        options={{
          title: t("tabs.agenda"),
          tabBarIcon: ({ focused }) => <TabIcon label="🗓️" focused={focused} />,
        }}
      />
      <Tabs.Screen
        name="bookings"
        options={{
          title: t("tabs.bookings"),
          tabBarIcon: ({ focused }) => <TabIcon label="📋" focused={focused} />,
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
          tabBarIcon: ({ focused }) => <TabIcon label="👤" focused={focused} />,
        }}
      />
      <Tabs.Screen name="booking/[id]" options={{ href: null }} />
      <Tabs.Screen name="pay/[bookingId]" options={{ href: null }} />
    </Tabs>
  );
}
