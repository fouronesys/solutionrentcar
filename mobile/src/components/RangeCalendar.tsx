/**
 * RangeCalendar — visual date-range picker presented as a bottom sheet modal.
 *
 * Props:
 *   visible    – controls Modal visibility
 *   start/end  – current selection (Date objects)
 *   onConfirm  – called with (newStart, newEnd) when user confirms
 *   onClose    – called when modal is dismissed without confirming
 */
import React, { useCallback, useEffect, useMemo, useState } from "react";
import {
  Dimensions,
  Modal,
  Pressable,
  ScrollView,
  StyleSheet,
  Text,
  View,
} from "react-native";
import { Ionicons } from "@expo/vector-icons";
import { useSafeAreaInsets } from "react-native-safe-area-context";
import { colors, font, radius, shadow, spacing, type } from "@/theme/colors";
import { useThemedStyles } from "@/theme/ThemeContext";
import { i18n, t } from "@/i18n";

// ─── Locale data ─────────────────────────────────────────────────────────────
const WD_ES = ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"];
const WD_EN = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
const MO_ES = [
  "Enero","Febrero","Marzo","Abril","Mayo","Junio",
  "Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre",
];
const MO_EN = [
  "January","February","March","April","May","June",
  "July","August","September","October","November","December",
];

// ─── Helpers ──────────────────────────────────────────────────────────────────
function sod(d: Date) {
  const c = new Date(d);
  c.setHours(0, 0, 0, 0);
  return c;
}
function addDays(d: Date, n: number) {
  const c = new Date(d);
  c.setDate(c.getDate() + n);
  return c;
}
function buildMonths(count: number) {
  const today = new Date();
  return Array.from({ length: count }, (_, i) => {
    const d = new Date(today.getFullYear(), today.getMonth() + i, 1);
    return { year: d.getFullYear(), month: d.getMonth() };
  });
}
function buildDays(year: number, month: number): (Date | null)[] {
  const first = new Date(year, month, 1);
  const last = new Date(year, month + 1, 0);
  const days: (Date | null)[] = Array.from({ length: first.getDay() }, () => null);
  for (let d = 1; d <= last.getDate(); d++) days.push(new Date(year, month, d));
  return days;
}

const { width: SW } = Dimensions.get("window");
const CELL_SIZE = Math.floor((SW - spacing.lg * 2) / 7);

// ─── Component ────────────────────────────────────────────────────────────────
interface Props {
  visible: boolean;
  start: Date;
  end: Date;
  onConfirm: (start: Date, end: Date) => void;
  onClose: () => void;
  /** Set of day timestamps (start-of-day ms) that are unavailable. */
  busyDays?: Set<number>;
}

export function RangeCalendar({
  visible,
  start,
  end,
  onConfirm,
  onClose,
  busyDays,
}: Props) {
  const styles = useThemedStyles(makeStyles);
  const insets = useSafeAreaInsets();
  const locale = i18n.locale === "en" ? "en" : "es";
  const WD = locale === "en" ? WD_EN : WD_ES;
  const MO = locale === "en" ? MO_EN : MO_ES;

  // Local selection state; synced with props on open
  const [lStart, setLStart] = useState(start);
  const [lEnd, setLEnd] = useState(end);
  const [selecting, setSelecting] = useState<"start" | "end">("start");
  const [conflict, setConflict] = useState(false);

  useEffect(() => {
    if (visible) {
      setLStart(start);
      setLEnd(end);
      setSelecting("start");
      setConflict(false);
    }
  }, [visible]); // eslint-disable-line react-hooks/exhaustive-deps

  const months = useMemo(() => buildMonths(7), []);
  const todayMs = sod(new Date()).getTime();

  const isBusy = useCallback(
    (dayMs: number) => busyDays?.has(dayMs) ?? false,
    [busyDays],
  );

  /** True if any day in [aMs, bMs] (inclusive) is busy. */
  const rangeHasBusy = useCallback(
    (aMs: number, bMs: number) => {
      if (!busyDays || busyDays.size === 0) return false;
      for (let t = aMs; t <= bMs; t += 86400000) {
        if (busyDays.has(t)) return true;
      }
      return false;
    },
    [busyDays],
  );

  const handleDay = useCallback(
    (day: Date) => {
      const d = sod(day);
      if (d.getTime() < todayMs) return;
      if (isBusy(d.getTime())) {
        setConflict(true);
        return;
      }
      setConflict(false);

      if (selecting === "start") {
        const ns = new Date(d.getFullYear(), d.getMonth(), d.getDate(), 10, 0, 0);
        setLStart(ns);
        if (d.getTime() >= sod(lEnd).getTime()) {
          setLEnd(new Date(d.getFullYear(), d.getMonth(), d.getDate() + 3, 18, 0, 0));
        }
        setSelecting("end");
      } else {
        if (d.getTime() <= sod(lStart).getTime()) {
          // Re-selecting start
          setLStart(new Date(d.getFullYear(), d.getMonth(), d.getDate(), 10, 0, 0));
          setSelecting("end");
        } else {
          if (rangeHasBusy(sod(lStart).getTime(), d.getTime())) {
            setConflict(true);
            return;
          }
          setLEnd(new Date(d.getFullYear(), d.getMonth(), d.getDate(), 18, 0, 0));
          setSelecting("start");
        }
      }
    },
    [selecting, lStart, lEnd, todayMs, isBusy, rangeHasBusy],
  );

  const sMs = sod(lStart).getTime();
  const eMs = sod(lEnd).getTime();
  const diffDays = Math.max(1, Math.ceil((eMs - sMs) / 86400000));
  const selectionBlocked = rangeHasBusy(sMs, eMs);

  const fmtShort = (d: Date) =>
    `${d.getDate()} ${MO[d.getMonth()].slice(0, 3)}`;

  return (
    <Modal
      visible={visible}
      animationType="slide"
      presentationStyle="pageSheet"
      onRequestClose={onClose}
    >
      <View
        style={[
          styles.modal,
          { paddingTop: insets.top + 8, paddingBottom: insets.bottom + 8 },
        ]}
      >
        {/* ── Header ──────────────────────────────────────────────── */}
        <View style={styles.header}>
          <Pressable onPress={onClose} style={styles.closeBtn} hitSlop={8}>
            <Ionicons name="close" size={22} color={colors.text} />
          </Pressable>
          <Text style={styles.headerTitle}>
            {locale === "en" ? "Choose dates" : "Elige tus fechas"}
          </Text>
          <View style={{ width: 38 }} />
        </View>

        {/* ── Range pills ─────────────────────────────────────────── */}
        <View style={styles.rangePills}>
          <Pressable
            onPress={() => setSelecting("start")}
            style={[styles.pill, selecting === "start" && styles.pillActive]}
          >
            <Text style={styles.pillLabel}>
              {locale === "en" ? "Pick-up" : "Recogida"}
            </Text>
            <Text
              style={[
                styles.pillDate,
                selecting === "start" && styles.pillDateActive,
              ]}
            >
              {fmtShort(lStart)}
            </Text>
          </Pressable>
          <View style={styles.pillArrow}>
            <Ionicons name="arrow-forward" size={14} color={colors.textMuted} />
            <Text style={styles.pillDays}>
              {diffDays} {locale === "en" ? "d" : "d"}
            </Text>
          </View>
          <Pressable
            onPress={() => setSelecting("end")}
            style={[styles.pill, selecting === "end" && styles.pillActive]}
          >
            <Text style={styles.pillLabel}>
              {locale === "en" ? "Return" : "Devolución"}
            </Text>
            <Text
              style={[
                styles.pillDate,
                selecting === "end" && styles.pillDateActive,
              ]}
            >
              {fmtShort(lEnd)}
            </Text>
          </Pressable>
        </View>

        {/* hint */}
        <Text style={styles.hint}>
          {selecting === "start"
            ? locale === "en"
              ? "Tap a day to set pick-up date"
              : "Toca para elegir fecha de recogida"
            : locale === "en"
            ? "Tap a day to set return date"
            : "Toca para elegir fecha de devolución"}
        </Text>

        {busyDays && busyDays.size > 0 ? (
          <View style={styles.legendRow}>
            <View style={styles.legendSwatch} />
            <Text style={styles.legendText}>{t("book.busyLegend")}</Text>
          </View>
        ) : null}

        {/* ── Calendar body ────────────────────────────────────────── */}
        <ScrollView
          showsVerticalScrollIndicator={false}
          contentContainerStyle={styles.calBody}
        >
          {/* Weekday headers */}
          <View style={styles.wdRow}>
            {WD.map((w) => (
              <Text key={w} style={styles.wdLabel}>
                {w}
              </Text>
            ))}
          </View>

          {months.map(({ year, month }) => {
            const days = buildDays(year, month);
            return (
              <View key={`${year}-${month}`}>
                <Text style={styles.monthLabel}>
                  {MO[month]} {year}
                </Text>
                <View style={styles.grid}>
                  {days.map((day, idx) => {
                    if (!day)
                      return <View key={`e${idx}`} style={styles.cell} />;
                    const dm = sod(day).getTime();
                    const isPast = dm < todayMs;
                    const busy = !isPast && isBusy(dm);
                    const isStart = dm === sMs;
                    const isEnd = dm === eMs;
                    const inRange = dm > sMs && dm < eMs;
                    const isToday = dm === todayMs;

                    return (
                      <Pressable
                        key={dm}
                        onPress={() => !isPast && handleDay(day)}
                        disabled={isPast || busy}
                        style={[
                          styles.cell,
                          inRange && styles.cellRange,
                          (isStart || isEnd) && styles.cellEndpoint,
                          isStart && styles.cellStartRadius,
                          isEnd && styles.cellEndRadius,
                          busy && styles.cellBusy,
                        ]}
                      >
                        <Text
                          style={[
                            styles.cellText,
                            isPast && styles.cellTextPast,
                            busy && styles.cellTextBusy,
                            isToday && !isStart && !isEnd && styles.cellTextToday,
                            (isStart || isEnd) && styles.cellTextSelected,
                          ]}
                        >
                          {day.getDate()}
                        </Text>
                        {isToday && !isStart && !isEnd ? (
                          <View style={styles.todayDot} />
                        ) : null}
                      </Pressable>
                    );
                  })}
                </View>
              </View>
            );
          })}
        </ScrollView>

        {/* ── Confirm bar ──────────────────────────────────────────── */}
        <View style={styles.confirmBar}>
          {conflict ? (
            <View style={styles.conflictRow}>
              <Ionicons name="alert-circle" size={16} color={colors.danger} />
              <Text style={styles.conflictText}>{t("book.busyConflict")}</Text>
            </View>
          ) : null}
          <Pressable
            style={[styles.confirmBtn, selectionBlocked && styles.confirmBtnDisabled]}
            disabled={selectionBlocked}
            onPress={() => {
              if (rangeHasBusy(sMs, eMs)) {
                setConflict(true);
                return;
              }
              onConfirm(lStart, lEnd);
            }}
          >
            <Ionicons name="checkmark" size={20} color="#FFF" />
            <Text style={styles.confirmText}>
              {locale === "en"
                ? `Confirm · ${diffDays} day${diffDays !== 1 ? "s" : ""}`
                : `Confirmar · ${diffDays} día${diffDays !== 1 ? "s" : ""}`}
            </Text>
          </Pressable>
        </View>
      </View>
    </Modal>
  );
}

// ─── Styles ───────────────────────────────────────────────────────────────────
const makeStyles = () =>
  StyleSheet.create({
    modal: { flex: 1, backgroundColor: colors.bg },

    header: {
      flexDirection: "row",
      alignItems: "center",
      justifyContent: "space-between",
      paddingHorizontal: spacing.lg,
      paddingBottom: 14,
    },
    closeBtn: {
      width: 38,
      height: 38,
      borderRadius: 19,
      backgroundColor: colors.card,
      alignItems: "center",
      justifyContent: "center",
    },
    headerTitle: { ...type.h3, color: colors.text },

    rangePills: {
      flexDirection: "row",
      alignItems: "center",
      marginHorizontal: spacing.lg,
      backgroundColor: colors.card,
      borderRadius: radius.xl,
      padding: 6,
      marginBottom: 10,
      ...shadow.sm,
    },
    pill: { flex: 1, alignItems: "center", paddingVertical: 10, borderRadius: radius.lg },
    pillActive: { backgroundColor: colors.ctaXLight },
    pillLabel: { ...type.label, color: colors.textMuted, marginBottom: 4 },
    pillDate: { ...type.h3, color: colors.text },
    pillDateActive: { color: colors.cta },
    pillArrow: { alignItems: "center", gap: 2 },
    pillDays: { ...type.small, color: colors.textMuted },

    hint: {
      ...type.small,
      color: colors.textMuted,
      textAlign: "center",
      marginBottom: 16,
    },

    calBody: {
      paddingHorizontal: spacing.lg,
      paddingBottom: 24,
    },
    wdRow: { flexDirection: "row", marginBottom: 6 },
    wdLabel: {
      width: CELL_SIZE,
      textAlign: "center",
      ...type.label,
      color: colors.textMuted,
    },
    monthLabel: {
      ...type.h3,
      color: colors.text,
      marginTop: 24,
      marginBottom: 12,
    },
    grid: { flexDirection: "row", flexWrap: "wrap" },

    cell: {
      width: CELL_SIZE,
      height: 44,
      alignItems: "center",
      justifyContent: "center",
    },
    cellRange: { backgroundColor: colors.ctaXLight },
    cellEndpoint: { backgroundColor: colors.cta },
    cellStartRadius: {
      borderTopLeftRadius: CELL_SIZE / 2,
      borderBottomLeftRadius: CELL_SIZE / 2,
    },
    cellEndRadius: {
      borderTopRightRadius: CELL_SIZE / 2,
      borderBottomRightRadius: CELL_SIZE / 2,
    },
    cellBusy: { opacity: 0.9 },
    cellText: { ...type.callout, color: colors.text },
    cellTextPast: { color: colors.textFaint },
    cellTextBusy: {
      color: colors.textFaint,
      textDecorationLine: "line-through",
    },
    cellTextToday: { fontFamily: font.bold, color: colors.cta },
    cellTextSelected: { color: "#FFF", fontFamily: font.bold },
    todayDot: {
      width: 4,
      height: 4,
      borderRadius: 2,
      backgroundColor: colors.cta,
      position: "absolute",
      bottom: 4,
    },

    confirmBar: {
      paddingHorizontal: spacing.lg,
      paddingTop: 12,
      paddingBottom: 8,
      backgroundColor: colors.card,
      borderTopWidth: 1,
      borderTopColor: colors.border,
      ...shadow.lg,
    },
    confirmBtn: {
      backgroundColor: colors.cta,
      height: 56,
      borderRadius: radius.lg,
      flexDirection: "row",
      alignItems: "center",
      justifyContent: "center",
      gap: 10,
      ...shadow.cta,
    },
    confirmBtnDisabled: { opacity: 0.5 },
    confirmText: { ...type.h3, color: "#FFF" },

    legendRow: {
      flexDirection: "row",
      alignItems: "center",
      justifyContent: "center",
      gap: 6,
      marginBottom: 10,
    },
    legendSwatch: {
      width: 12,
      height: 12,
      borderRadius: 3,
      backgroundColor: colors.textFaint,
    },
    legendText: { ...type.small, color: colors.textMuted },

    conflictRow: {
      flexDirection: "row",
      alignItems: "center",
      gap: 6,
      marginBottom: 10,
    },
    conflictText: { ...type.small, color: colors.danger, flex: 1 },
  });
