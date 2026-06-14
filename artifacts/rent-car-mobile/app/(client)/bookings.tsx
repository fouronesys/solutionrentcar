import React, { useCallback, useEffect, useState } from "react";
import {
  FlatList,
  Pressable,
  RefreshControl,
  StyleSheet,
  Text,
  View,
} from "react-native";
import { useFocusEffect, useRouter } from "expo-router";
import { SafeAreaView } from "react-native-safe-area-context";
import { Loading } from "@/components/Loading";
import { EmptyState } from "@/components/EmptyState";
import { Button } from "@/components/Button";
import { api, ApiError } from "@/api/client";
import type { Booking } from "@/api/types";
import { bookingStatus, colors, radius, shadow, spacing } from "@/theme/colors";
import { i18n, t } from "@/i18n";
import { money, shortDate } from "@/utils/format";
import { useAuth } from "@/auth/AuthContext";

function LoginPrompt() {
  const router = useRouter();
  return (
    <View style={styles.prompt}>
      <View style={styles.promptIcon}><Text style={{ fontSize: 48 }}>📅</Text></View>
      <Text style={styles.promptTitle}>{t("login.requiredTitle")}</Text>
      <Text style={styles.promptSub}>{t("login.requiredSubtitle")}</Text>
      <Button title={t("login.goToLogin")} onPress={() => router.push("/login/client")} style={{ marginBottom: 12 }} size="lg" />
      <Pressable onPress={() => router.push("/register/client")}>
        <Text style={styles.registerLink}>
          {t("login.noAccount")} <Text style={styles.registerLinkBold}>{t("login.createAccount")}</Text>
        </Text>
      </Pressable>
    </View>
  );
}

function BookingCard({ booking, onPress }: { booking: Booking; onPress: () => void }) {
  const s = bookingStatus[Number(booking.status ?? 0)];
  const locale = i18n.locale === "en" ? "en" : "es";
  const total = Number(booking.total ?? 0);
  const paid = Number(booking.payment ?? 0);
  const balance = Math.max(0, total - paid);

  return (
    <Pressable onPress={onPress} style={({ pressed }) => [styles.card, pressed && { opacity: 0.92 }]}>
      <View style={styles.cardHeader}>
        <View>
          <Text style={styles.cardCode}>#{booking.code ?? booking.id}</Text>
          <Text style={styles.cardDates}>{shortDate(booking.start_at)} → {shortDate(booking.end_at)}</Text>
        </View>
        {s ? (
          <View style={[styles.statusPill, { backgroundColor: s.bg }]}>
            <Text style={[styles.statusText, { color: s.color }]}>{s[locale]}</Text>
          </View>
        ) : null}
      </View>
      <View style={styles.cardDivider} />
      <View style={styles.cardFooter}>
        <View style={styles.cardAmount}>
          <Text style={styles.amountLabel}>{t("booking.total")}</Text>
          <Text style={styles.amountValue}>{money(total)}</Text>
        </View>
        {balance > 0 ? (
          <View style={styles.cardAmount}>
            <Text style={styles.amountLabel}>{t("booking.balance")}</Text>
            <Text style={[styles.amountValue, { color: colors.danger }]}>{money(balance)}</Text>
          </View>
        ) : (
          <View style={[styles.paidBadge]}>
            <Text style={styles.paidText}>✓ {locale === "en" ? "Paid" : "Pagado"}</Text>
          </View>
        )}
        <Text style={styles.chevron}>›</Text>
      </View>
    </Pressable>
  );
}

export default function BookingsList() {
  const router = useRouter();
  const { role, bootstrapped } = useAuth();
  const [items, setItems] = useState<Booking[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [err, setErr] = useState<string | null>(null);

  const load = useCallback(async () => {
    if (!role) return;
    setErr(null);
    try {
      const r = await api.get<{ bookings: Booking[] }>("/bookings", { limit: 50 });
      setItems(r.bookings ?? []);
    } catch (e) {
      setErr(e instanceof ApiError ? e.message : t("common.error"));
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, [role]);

  useEffect(() => {
    if (role) load();
    else setLoading(false);
  }, [role, load]);

  useFocusEffect(useCallback(() => { if (role) load(); }, [role, load]));

  if (!bootstrapped || (role && loading)) return <Loading />;

  if (!role) {
    return (
      <SafeAreaView style={styles.screen} edges={["top"]}>
        <View style={styles.pageHeader}>
          <Text style={styles.pageTitle}>{t("booking.myBookings")}</Text>
        </View>
        <LoginPrompt />
      </SafeAreaView>
    );
  }

  return (
    <SafeAreaView style={styles.screen} edges={["top"]}>
      <View style={styles.pageHeader}>
        <Text style={styles.pageTitle}>{t("booking.myBookings")}</Text>
        <Text style={styles.pageCount}>{items.length}</Text>
      </View>
      {err ? (
        <View style={styles.errBox}><Text style={styles.errText}>⚠️  {err}</Text></View>
      ) : null}
      <FlatList
        contentContainerStyle={styles.list}
        data={items}
        keyExtractor={(b) => String(b.id)}
        refreshControl={
          <RefreshControl
            refreshing={refreshing}
            onRefresh={() => { setRefreshing(true); load(); }}
            tintColor={colors.primaryDark}
          />
        }
        ListEmptyComponent={
          <EmptyState title={t("booking.noneClient")} subtitle={t("cars.title")} icon="📋" />
        }
        renderItem={({ item }) => (
          <BookingCard
            booking={item}
            onPress={() => router.push({ pathname: "/(client)/booking/[id]", params: { id: String(item.id) } })}
          />
        )}
      />
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  screen: { flex: 1, backgroundColor: colors.bg },
  pageHeader: {
    flexDirection: "row",
    alignItems: "center",
    paddingHorizontal: spacing.lg,
    paddingTop: spacing.md,
    paddingBottom: spacing.lg,
    borderBottomWidth: 1,
    borderBottomColor: colors.border,
    backgroundColor: colors.card,
  },
  pageTitle: { flex: 1, fontSize: 20, fontWeight: "800", color: colors.text },
  pageCount: {
    fontSize: 13,
    color: colors.textMuted,
    fontWeight: "600",
    backgroundColor: colors.borderLight,
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: radius.full,
  },
  errBox: { margin: spacing.lg, padding: 12, backgroundColor: colors.dangerBg, borderRadius: radius.md },
  errText: { color: colors.danger, fontSize: 13, fontWeight: "500" },
  list: { padding: spacing.lg, paddingTop: spacing.md },

  card: {
    backgroundColor: colors.card,
    borderRadius: radius.lg,
    marginBottom: spacing.md,
    overflow: "hidden",
    ...shadow.md,
  },
  cardHeader: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "flex-start",
    padding: spacing.lg,
    paddingBottom: spacing.md,
  },
  cardCode: { fontSize: 16, fontWeight: "800", color: colors.text },
  cardDates: { fontSize: 13, color: colors.textMuted, marginTop: 3 },
  statusPill: { paddingHorizontal: 10, paddingVertical: 4, borderRadius: radius.full },
  statusText: { fontSize: 12, fontWeight: "700" },
  cardDivider: { height: 1, backgroundColor: colors.borderLight, marginHorizontal: spacing.lg },
  cardFooter: {
    flexDirection: "row",
    alignItems: "center",
    padding: spacing.lg,
    paddingTop: spacing.md,
  },
  cardAmount: { marginRight: 20 },
  amountLabel: { fontSize: 11, color: colors.textMuted, fontWeight: "600", textTransform: "uppercase", letterSpacing: 0.4 },
  amountValue: { fontSize: 16, fontWeight: "800", color: colors.text, marginTop: 2 },
  paidBadge: {
    backgroundColor: colors.successBg,
    paddingHorizontal: 10,
    paddingVertical: 5,
    borderRadius: radius.full,
  },
  paidText: { color: colors.success, fontSize: 12, fontWeight: "700" },
  chevron: { marginLeft: "auto", fontSize: 24, color: colors.textMuted, fontWeight: "300" },

  prompt: { flex: 1, alignItems: "center", justifyContent: "center", padding: spacing.xxl },
  promptIcon: {
    width: 88,
    height: 88,
    borderRadius: 44,
    backgroundColor: colors.primaryXLight,
    alignItems: "center",
    justifyContent: "center",
    marginBottom: 20,
  },
  promptTitle: { fontSize: 20, fontWeight: "800", color: colors.text, textAlign: "center", marginBottom: 8 },
  promptSub: { color: colors.textMuted, fontSize: 14, textAlign: "center", marginBottom: 28, lineHeight: 20 },
  registerLink: { color: colors.textMuted, fontSize: 14 },
  registerLinkBold: { color: colors.primaryDark, fontWeight: "700" },
});
