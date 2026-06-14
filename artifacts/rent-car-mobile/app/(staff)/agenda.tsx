import React, { useCallback, useEffect, useState } from "react";
import { Pressable, RefreshControl, ScrollView, StyleSheet, Text, View } from "react-native";
import { useFocusEffect, useRouter } from "expo-router";
import { SafeAreaView } from "react-native-safe-area-context";
import { EmptyState } from "@/components/EmptyState";
import { Loading } from "@/components/Loading";
import { api, ApiError } from "@/api/client";
import type { Agenda, AgendaItem } from "@/api/types";
import { colors, radius, shadow, spacing } from "@/theme/colors";
import { t } from "@/i18n";
import { dateTime, todayIso } from "@/utils/format";

function AgendaCard({ item, label, onPress }: { item: AgendaItem; label: string; onPress: () => void }) {
  return (
    <Pressable onPress={onPress} style={({ pressed }) => [styles.agendaCard, pressed && { opacity: 0.9 }]}>
      <View style={styles.agendaLeft}>
        <Text style={styles.agendaTypeLabel}>{label}</Text>
        <Text style={styles.agendaCode}>#{item.booking.code ?? item.booking.id}</Text>
        <Text style={styles.agendaCar}>
          {item.car?.brand ? `${item.car.brand} ` : ""}{item.car?.name ?? item.car?.model ?? "—"}
        </Text>
        <Text style={styles.agendaClient}>
          👤 {item.client?.name ?? ""} {item.client?.lastname ?? ""}
          {item.client?.phone ? ` · ${item.client.phone}` : ""}
        </Text>
        <Text style={styles.agendaTime}>🕐 {dateTime(label === "Entrega" || label === "Delivery" ? item.booking.start_at : item.booking.end_at)}</Text>
      </View>
      <Text style={styles.agendaChevron}>›</Text>
    </Pressable>
  );
}

export default function AgendaScreen() {
  const router = useRouter();
  const [data, setData] = useState<Agenda | null>(null);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [err, setErr] = useState<string | null>(null);

  const load = useCallback(async () => {
    setErr(null);
    try {
      const r = await api.get<Agenda>("/agenda", { date: todayIso() });
      setData(r);
    } catch (e) {
      setErr(e instanceof ApiError ? e.message : t("common.error"));
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, []);

  useEffect(() => { load(); }, [load]);
  useFocusEffect(useCallback(() => { load(); }, [load]));

  const navigate = (id: number) =>
    router.push({ pathname: "/(staff)/booking/[id]", params: { id: String(id) } });

  if (loading) return <Loading />;

  const today = new Date().toLocaleDateString("es-ES", { weekday: "long", day: "numeric", month: "long" });

  return (
    <SafeAreaView style={styles.screen} edges={["top"]}>
      {/* Header */}
      <View style={styles.header}>
        <View style={styles.headerIcon}><Text style={{ fontSize: 24 }}>🗓️</Text></View>
        <View>
          <Text style={styles.headerSub}>{t("agenda.title")}</Text>
          <Text style={styles.headerDate}>{today}</Text>
        </View>
      </View>

      {err ? <View style={styles.errBox}><Text style={styles.errText}>⚠️  {err}</Text></View> : null}

      <ScrollView
        contentContainerStyle={styles.body}
        showsVerticalScrollIndicator={false}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={() => { setRefreshing(true); load(); }} tintColor={colors.primaryDark} />
        }
      >
        {/* Stats */}
        <View style={styles.statsRow}>
          <View style={[styles.statCard, { borderColor: colors.success }]}>
            <Text style={styles.statNum}>{data?.deliveries?.length ?? 0}</Text>
            <Text style={[styles.statLabel, { color: colors.success }]}>📦 {t("agenda.deliveries")}</Text>
          </View>
          <View style={[styles.statCard, { borderColor: colors.info }]}>
            <Text style={styles.statNum}>{data?.returns?.length ?? 0}</Text>
            <Text style={[styles.statLabel, { color: colors.info }]}>🔁 {t("agenda.returns")}</Text>
          </View>
        </View>

        {/* Deliveries */}
        <View style={styles.groupHeader}>
          <View style={[styles.groupDot, { backgroundColor: colors.success }]} />
          <Text style={styles.groupTitle}>{t("agenda.deliveries")}</Text>
        </View>
        {data?.deliveries?.length ? (
          data.deliveries.map((it) => (
            <AgendaCard
              key={`d-${it.booking.id}`}
              item={it}
              label="Entrega"
              onPress={() => navigate(it.booking.id)}
            />
          ))
        ) : (
          <EmptyState title={t("agenda.noDeliveries")} />
        )}

        {/* Returns */}
        <View style={[styles.groupHeader, { marginTop: spacing.lg }]}>
          <View style={[styles.groupDot, { backgroundColor: colors.info }]} />
          <Text style={styles.groupTitle}>{t("agenda.returns")}</Text>
        </View>
        {data?.returns?.length ? (
          data.returns.map((it) => (
            <AgendaCard
              key={`r-${it.booking.id}`}
              item={it}
              label="Devolución"
              onPress={() => navigate(it.booking.id)}
            />
          ))
        ) : (
          <EmptyState title={t("agenda.noReturns")} />
        )}
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  screen: { flex: 1, backgroundColor: colors.bg },
  header: {
    flexDirection: "row",
    alignItems: "center",
    paddingHorizontal: spacing.lg,
    paddingVertical: spacing.md,
    backgroundColor: colors.dark,
    gap: 14,
  },
  headerIcon: {
    width: 48, height: 48, borderRadius: 14,
    backgroundColor: "rgba(255,255,255,0.1)",
    alignItems: "center", justifyContent: "center",
  },
  headerSub: { fontSize: 11, color: "rgba(255,255,255,0.5)", fontWeight: "700", textTransform: "uppercase", letterSpacing: 0.8 },
  headerDate: { fontSize: 16, color: "#fff", fontWeight: "700", marginTop: 2, textTransform: "capitalize" },

  errBox: { margin: spacing.lg, padding: 12, backgroundColor: colors.dangerBg, borderRadius: radius.md },
  errText: { color: colors.danger, fontSize: 13 },

  body: { padding: spacing.lg, paddingBottom: 32 },

  statsRow: { flexDirection: "row", gap: 12, marginBottom: spacing.lg },
  statCard: {
    flex: 1,
    backgroundColor: colors.card,
    borderRadius: radius.lg,
    padding: spacing.lg,
    alignItems: "center",
    borderWidth: 2,
    ...shadow.sm,
  },
  statNum: { fontSize: 32, fontWeight: "800", color: colors.text },
  statLabel: { fontSize: 13, fontWeight: "700", marginTop: 4 },

  groupHeader: { flexDirection: "row", alignItems: "center", gap: 8, marginBottom: spacing.md },
  groupDot: { width: 8, height: 8, borderRadius: 4 },
  groupTitle: { fontSize: 14, fontWeight: "800", color: colors.text, textTransform: "uppercase", letterSpacing: 0.5 },

  agendaCard: {
    backgroundColor: colors.card,
    borderRadius: radius.lg,
    marginBottom: 10,
    padding: spacing.lg,
    flexDirection: "row",
    alignItems: "center",
    ...shadow.md,
  },
  agendaLeft: { flex: 1 },
  agendaTypeLabel: { fontSize: 10, fontWeight: "700", color: colors.textMuted, textTransform: "uppercase", letterSpacing: 0.8, marginBottom: 4 },
  agendaCode: { fontSize: 16, fontWeight: "800", color: colors.text },
  agendaCar: { fontSize: 14, color: colors.textSecondary, marginTop: 2 },
  agendaClient: { fontSize: 13, color: colors.textMuted, marginTop: 6 },
  agendaTime: { fontSize: 12, color: colors.primaryDark, fontWeight: "700", marginTop: 4 },
  agendaChevron: { fontSize: 24, color: colors.textMuted, fontWeight: "300", marginLeft: 12 },
});
