/**
 * Inicio — home screen for the client area.
 * Shows a welcome message, quick CTA to browse cars, and an upcoming reservation card.
 */
import React, { useCallback, useEffect, useState } from "react";
import {
  Pressable,
  RefreshControl,
  ScrollView,
  StyleSheet,
  Text,
  View,
} from "react-native";
import { SafeAreaView } from "react-native-safe-area-context";
import { useFocusEffect, useRouter } from "expo-router";
import { useTabBarScroll } from "@/components/TabBarScrollContext";
import { Ionicons } from "@expo/vector-icons";
import { StatusBar } from "expo-status-bar";
import { BellButton, ScreenHeader } from "@/components/ScreenHeader";
import { api, ApiError } from "@/api/client";
import { Image as ExpoImage } from "expo-image";
import { useAuth } from "@/auth/AuthContext";
import { useNotificationsCtx } from "@/notifications/NotificationsContext";
import { bookingStatus, colors, font, radius, shadow, spacing, type } from "@/theme/colors";
import { useThemedStyles, useTheme } from "@/theme/ThemeContext";
import type { Booking, Car } from "@/api/types";
import { i18n, t } from "@/i18n";
import { money, shortDate } from "@/utils/format";

function NextBookingCard({ booking }: { booking: Booking }) {
  const styles = useThemedStyles(makeStyles);
  const router = useRouter();
  const s = bookingStatus[Number(booking.status ?? 0)];
  const locale = i18n.locale === "en" ? "en" : "es";
  const car = (booking as any).car;

  return (
    <Pressable
      onPress={() =>
        router.push({ pathname: "/(client)/booking/[id]", params: { id: String(booking.id) } })
      }
      style={styles.nextCard}
    >
      {/* label */}
      <View style={styles.nextCardTopRow}>
        <Text style={styles.nextCardLabel}>
          {locale === "en" ? "UPCOMING BOOKING" : "PRÓXIMA RESERVA"}
        </Text>
        {s ? (
          <View style={[styles.statusPill, { backgroundColor: s.bg }]}>
            <Text style={[styles.statusPillText, { color: s.color }]}>{s[locale]}</Text>
          </View>
        ) : null}
      </View>

      <Text style={styles.nextCardCar}>
        {car?.brand ? `${car.brand} ` : ""}
        {car?.name ?? car?.model ?? `#${booking.code ?? booking.id}`}
      </Text>

      <View style={styles.nextCardRow}>
        <View style={styles.nextCardCell}>
          <Text style={styles.nextCardCellLabel}>
            {locale === "en" ? "Pickup" : "Recogida"}
          </Text>
          <Text style={styles.nextCardCellValue}>{shortDate(booking.start_at)}</Text>
          {booking.place_start ? (
            <Text style={styles.nextCardCellSub} numberOfLines={1}>
              {booking.place_start}
            </Text>
          ) : null}
        </View>
        <View style={styles.nextCardDivider} />
        <View style={styles.nextCardCell}>
          <Text style={styles.nextCardCellLabel}>
            {locale === "en" ? "Return" : "Devolución"}
          </Text>
          <Text style={styles.nextCardCellValue}>{shortDate(booking.end_at)}</Text>
          {booking.place_end ? (
            <Text style={styles.nextCardCellSub} numberOfLines={1}>
              {booking.place_end}
            </Text>
          ) : null}
        </View>
        <Pressable
          onPress={() =>
            router.push({ pathname: "/(client)/booking/[id]", params: { id: String(booking.id) } })
          }
          style={styles.nextCardArrow}
        >
          <Ionicons name="chevron-forward" size={20} color={colors.onDark} />
        </Pressable>
      </View>
    </Pressable>
  );
}

export default function HomeScreen() {
  const styles = useThemedStyles(makeStyles);
  const { isDark } = useTheme();
  const router = useRouter();
  const { user, role } = useAuth();
  const { unread } = useNotificationsCtx();
  const { onScroll, showTabBar } = useTabBarScroll();
  useFocusEffect(useCallback(() => { showTabBar(); }, [showTabBar]));
  const [upcoming, setUpcoming] = useState<Booking | null>(null);
  const [featured, setFeatured] = useState<Car[]>([]);
  const [refreshing, setRefreshing] = useState(false);
  const isGuest = !role;

  const load = useCallback(async () => {
    try {
      if (isGuest) {
        // Guests: show a "Destacados" carousel of available cars.
        const r = await api.get<{ cars: Car[] }>("/cars", { limit: 5, status: 0 });
        setFeatured(r.cars ?? []);
      } else {
        const r = await api.get<{ bookings: Booking[] }>("/bookings", { limit: 1, status: 1 });
        setUpcoming(r.bookings?.[0] ?? null);
      }
    } catch {
      // silent
    } finally {
      setRefreshing(false);
    }
  }, [isGuest]);

  useEffect(() => { load(); }, [load]);

  const locale = i18n.locale === "en" ? "en" : "es";
  const firstName = user?.name?.split(" ")[0] ?? "";

  return (
    <SafeAreaView style={styles.screen} edges={["top"]}>
      <StatusBar style={isDark ? "light" : "dark"} />
      <ScreenHeader
        title="Yowell Rent-Car"
        subtitle={locale === "en" ? "Your next move starts here." : "Tu próximo movimiento."}
        right={
          <BellButton
            unread={unread}
            onPress={() => router.push("/(client)/notifications")}
          />
        }
      />

      <ScrollView
        contentContainerStyle={styles.body}
        showsVerticalScrollIndicator={false}
        onScroll={onScroll}
        scrollEventThrottle={16}
        refreshControl={
          <RefreshControl
            refreshing={refreshing}
            onRefresh={() => { setRefreshing(true); load(); }}
            tintColor={colors.cta}
          />
        }
      >
        {/* Greeting */}
        <View style={styles.greetingBlock}>
          <Text style={styles.eyebrow}>
            {locale === "en" ? "WELCOME" : "BIENVENIDO"}
            {firstName ? ` · ${firstName.toUpperCase()}` : ""}
          </Text>
          <Text style={styles.heading}>
            {locale === "en" ? "Move your\nway." : "Muévete a\ntu manera."}
          </Text>
        </View>

        {/* CTA cards row */}
        <View style={styles.ctaRow}>
          <Pressable style={styles.ctaCardRed} onPress={() => router.push("/(client)/cars")}>
            <Ionicons name="car-sport" size={28} color="#FFFFFF" />
            <Text style={styles.ctaCardRedTitle}>
              {locale === "en" ? "Browse cars" : "Ver flota"}
            </Text>
            <Text style={styles.ctaCardRedSub}>
              {locale === "en" ? "Available now" : "Disponibles ahora"}
            </Text>
          </Pressable>
          {isGuest ? (
            <Pressable style={styles.ctaCardDark} onPress={() => router.push("/(client)/locations")}>
              <Ionicons name="location-outline" size={28} color={colors.onDark} />
              <Text style={styles.ctaCardDarkTitle}>{t("home.ourLocations")}</Text>
              <Text style={styles.ctaCardDarkSub}>{t("home.ourLocationsSub")}</Text>
            </Pressable>
          ) : (
            <Pressable style={styles.ctaCardDark} onPress={() => router.push("/(client)/bookings")}>
              <Ionicons name="calendar-outline" size={28} color={colors.onDark} />
              <Text style={styles.ctaCardDarkTitle}>
                {locale === "en" ? "My bookings" : "Mis reservas"}
              </Text>
              <Text style={styles.ctaCardDarkSub}>
                {locale === "en" ? "Track & manage" : "Seguimiento"}
              </Text>
            </Pressable>
          )}
        </View>

        {/* Guest banner — compact */}
        {isGuest ? (
          <View style={styles.guestBanner}>
            <Ionicons name="sparkles-outline" size={20} color={colors.cta} />
            <Text style={styles.guestBannerText} numberOfLines={2}>
              {t("home.guestBannerText")}
            </Text>
            <Pressable style={styles.guestBannerBtn} onPress={() => router.push("/login/client")}>
              <Text style={styles.guestBannerBtnText}>{t("home.guestBannerCta")}</Text>
            </Pressable>
          </View>
        ) : null}

        {/* Destacados — featured carousel (guests) */}
        {isGuest ? (
          <View style={styles.sectionNoPad}>
            <Text style={styles.sectionTitlePad}>{t("home.featuredTitle")}</Text>
            {featured.length ? (
              <ScrollView
                horizontal
                showsHorizontalScrollIndicator={false}
                contentContainerStyle={styles.featuredRow}
              >
                {featured.map((car) => (
                  <Pressable
                    key={car.id}
                    style={styles.featuredCard}
                    onPress={() =>
                      router.push({ pathname: "/(client)/car/[id]", params: { id: String(car.id) } })
                    }
                  >
                    {car.image ? (
                      <ExpoImage
                        source={{ uri: car.image }}
                        style={styles.featuredImg}
                        contentFit="cover"
                        transition={150}
                        cachePolicy="memory-disk"
                      />
                    ) : (
                      <View style={styles.featuredImgPlaceholder}>
                        <Ionicons name="car-sport" size={30} color={colors.textFaint} />
                      </View>
                    )}
                    <Text style={styles.featuredName} numberOfLines={1}>
                      {car.brand ? `${car.brand} ` : ""}{car.name ?? car.model ?? ""}
                    </Text>
                    <View style={styles.featuredPriceRow}>
                      <Text style={styles.featuredPrice}>
                        {money(Number(car.price_day ?? car.price ?? 0))}
                      </Text>
                      <Text style={styles.featuredPer}>{t("home.perDay")}</Text>
                    </View>
                  </Pressable>
                ))}
              </ScrollView>
            ) : (
              <Text style={styles.featuredEmpty}>{t("home.featuredEmpty")}</Text>
            )}
          </View>
        ) : null}

        {/* Upcoming booking */}
        {upcoming ? (
          <View style={styles.section}>
            <Text style={styles.sectionTitle}>
              {locale === "en" ? "Upcoming" : "Próxima reserva"}
            </Text>
            <NextBookingCard booking={upcoming} />
          </View>
        ) : null}

        {/* Quick links */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>
            {locale === "en" ? "Quick access" : "Acceso rápido"}
          </Text>
          {(isGuest
            ? [
                {
                  icon: "location-outline" as const,
                  label: t("home.ourLocations"),
                  sub: t("home.ourLocationsSub"),
                  route: "/(client)/locations" as const,
                },
                {
                  icon: "car-sport-outline" as const,
                  label: t("home.exploreFleet"),
                  sub: t("home.exploreFleetSub"),
                  route: "/(client)/cars" as const,
                },
              ]
            : [
                {
                  icon: "location-outline" as const,
                  label: locale === "en" ? "Our locations" : "Nuestras ubicaciones",
                  sub: locale === "en" ? "Find the nearest branch" : "Sucursal más cercana",
                  route: "/(client)/locations" as const,
                },
                {
                  icon: "document-text-outline" as const,
                  label: locale === "en" ? "My bookings" : "Mis reservas",
                  sub: locale === "en" ? "View all reservations" : "Ver todas las reservas",
                  route: "/(client)/bookings" as const,
                },
                {
                  icon: "notifications-outline" as const,
                  label: locale === "en" ? "Notifications" : "Notificaciones",
                  sub: locale === "en" ? "Updates and alerts" : "Avisos y actualizaciones",
                  route: "/(client)/notifications" as const,
                },
              ]
          ).map((item) => (
            <Pressable
              key={item.route}
              style={styles.quickRow}
              onPress={() => router.push(item.route as any)}
            >
              <View style={styles.quickIcon}>
                <Ionicons name={item.icon} size={20} color={colors.cta} />
              </View>
              <View style={{ flex: 1 }}>
                <Text style={styles.quickLabel}>{item.label}</Text>
                <Text style={styles.quickSub}>{item.sub}</Text>
              </View>
              <Ionicons name="chevron-forward" size={18} color={colors.textFaint} />
            </Pressable>
          ))}
        </View>
      </ScrollView>
    </SafeAreaView>
  );
}

const makeStyles = () => StyleSheet.create({
  screen: { flex: 1, backgroundColor: colors.bg },
  body: { paddingBottom: 40 },

  greetingBlock: {
    paddingHorizontal: spacing.xl,
    paddingTop: spacing.xxl,
    paddingBottom: spacing.lg,
  },
  eyebrow: { ...type.label, color: colors.cta, marginBottom: 8 },
  heading: { ...type.display, color: colors.text, fontSize: 34, lineHeight: 40, letterSpacing: -0.8 },

  ctaRow: {
    flexDirection: "row",
    gap: 12,
    paddingHorizontal: spacing.xl,
    marginBottom: spacing.xl,
  },
  ctaCardRed: {
    flex: 1,
    backgroundColor: colors.cta,
    borderRadius: radius.xl,
    padding: 20,
    gap: 6,
    ...shadow.cta,
  },
  ctaCardRedTitle: { ...type.title, color: "#FFFFFF", marginTop: 6 },
  ctaCardRedSub: { ...type.caption, color: "rgba(255,255,255,0.75)" },
  ctaCardDark: {
    flex: 1,
    backgroundColor: colors.darkCard,
    borderRadius: radius.xl,
    padding: 20,
    gap: 6,
    ...shadow.md,
  },
  ctaCardDarkTitle: { ...type.title, color: colors.onDark, marginTop: 6 },
  ctaCardDarkSub: { ...type.caption, color: colors.onDarkMuted },

  section: { paddingHorizontal: spacing.xl, marginBottom: spacing.xl },
  sectionTitle: { ...type.h3, color: colors.text, marginBottom: 12 },

  // Next booking card (dark)
  nextCard: {
    backgroundColor: colors.darkCard,
    borderRadius: radius.xl,
    padding: spacing.lg,
    ...shadow.md,
  },
  nextCardTopRow: { flexDirection: "row", justifyContent: "space-between", alignItems: "center", marginBottom: 8 },
  nextCardLabel: { ...type.label, color: colors.ctaLight, fontSize: 10, letterSpacing: 0.8 },
  statusPill: {
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: radius.full,
  },
  statusPillText: { ...type.small, fontFamily: font.semibold },
  nextCardCar: { ...type.h2, color: colors.onDark, marginBottom: 16 },
  nextCardRow: { flexDirection: "row", alignItems: "center" },
  nextCardCell: { flex: 1 },
  nextCardCellLabel: { ...type.label, color: colors.onDarkMuted, fontSize: 9, letterSpacing: 0.6, marginBottom: 4 },
  nextCardCellValue: { ...type.title, color: colors.onDark },
  nextCardCellSub: { ...type.caption, color: colors.onDarkMuted, marginTop: 2 },
  nextCardDivider: { width: 1, height: 36, backgroundColor: "rgba(255,255,255,0.12)", marginHorizontal: 16 },
  nextCardArrow: {
    width: 36,
    height: 36,
    borderRadius: radius.full,
    backgroundColor: "rgba(255,255,255,0.08)",
    alignItems: "center",
    justifyContent: "center",
  },

  // Guest banner — compact
  guestBanner: {
    flexDirection: "row",
    alignItems: "center",
    gap: 12,
    backgroundColor: colors.ctaXLight,
    marginHorizontal: spacing.xl,
    marginBottom: spacing.xl,
    borderRadius: radius.lg,
    paddingVertical: 12,
    paddingHorizontal: 16,
    borderWidth: 1,
    borderColor: colors.ctaLight,
  },
  guestBannerText: { ...type.callout, color: colors.text, flex: 1 },
  guestBannerBtn: {
    paddingHorizontal: 14,
    paddingVertical: 8,
    borderRadius: radius.full,
    backgroundColor: colors.cta,
    ...shadow.cta,
  },
  guestBannerBtnText: { ...type.captionMed, color: "#FFFFFF", fontFamily: font.bold },

  // Featured carousel
  sectionNoPad: { marginBottom: spacing.xl },
  sectionTitlePad: { ...type.h3, color: colors.text, marginBottom: 12, paddingHorizontal: spacing.xl },
  featuredRow: { gap: 12, paddingHorizontal: spacing.xl },
  featuredCard: {
    width: 180,
    backgroundColor: colors.card,
    borderRadius: radius.lg,
    padding: 10,
    ...shadow.sm,
  },
  featuredImg: { width: "100%", height: 100, borderRadius: radius.md, marginBottom: 8 },
  featuredImgPlaceholder: {
    width: "100%",
    height: 100,
    borderRadius: radius.md,
    marginBottom: 8,
    backgroundColor: colors.borderLight,
    alignItems: "center",
    justifyContent: "center",
  },
  featuredName: { ...type.bodyMed, color: colors.text },
  featuredPriceRow: { flexDirection: "row", alignItems: "baseline", marginTop: 4 },
  featuredPrice: { fontFamily: font.extrabold, fontSize: 16, color: colors.text },
  featuredPer: { ...type.caption, color: colors.textMuted, marginLeft: 2 },
  featuredEmpty: { ...type.callout, color: colors.textMuted, paddingHorizontal: spacing.xl },

  // Quick links
  quickRow: {
    flexDirection: "row",
    alignItems: "center",
    gap: 14,
    backgroundColor: colors.card,
    borderRadius: radius.lg,
    paddingVertical: 14,
    paddingHorizontal: 16,
    marginBottom: 8,
    ...shadow.xs,
  },
  quickIcon: {
    width: 42,
    height: 42,
    borderRadius: radius.md,
    backgroundColor: colors.ctaXLight,
    alignItems: "center",
    justifyContent: "center",
  },
  quickLabel: { ...type.bodyMed, color: colors.text },
  quickSub: { ...type.caption, color: colors.textMuted, marginTop: 1 },
});
