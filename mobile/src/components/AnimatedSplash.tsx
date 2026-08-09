import React, { useEffect, useRef, useState } from "react";
import { Dimensions, Image, Platform, StyleSheet, Text, View } from "react-native";
import { LinearGradient } from "expo-linear-gradient";
import Animated, {
  Easing,
  cancelAnimation,
  interpolate,
  runOnJS,
  useAnimatedStyle,
  useSharedValue,
  withDelay,
  withRepeat,
  withSequence,
  withSpring,
  withTiming,
} from "react-native-reanimated";
import { colors, font } from "@/theme/colors";

const logo = require("../../assets/images/logo.png");

const LOGO_SIZE = 196;
const { width: SCREEN_W } = Dimensions.get("window");

type Props = {
  /** True once fonts + auth bootstrap are ready and the app can be revealed. */
  appReady: boolean;
  /** Called after the exit animation completes so the parent can unmount us. */
  onFinish: () => void;
  /** Minimum time the splash stays on screen so the animation is appreciated. */
  minDuration?: number;
  /** Hard cap so a stalled bootstrap never traps the user on the splash. */
  maxDuration?: number;
};

export default function AnimatedSplash({
  appReady,
  onFinish,
  minDuration = 1900,
  maxDuration = 6000,
}: Props) {
  // Web: runOnJS callbacks inside Reanimated exit animations don't fire reliably.
  // Skip the custom splash entirely — the OS native splash already hides itself.
  useEffect(() => {
    if (Platform.OS === "web") { onFinish(); }
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);
  if (Platform.OS === "web") return null;

  const [minElapsed, setMinElapsed] = useState(false);
  const [forced, setForced] = useState(false);
  const exiting = useRef(false);

  // Shared values
  const root = useSharedValue(1); // exit fade
  const rootScale = useSharedValue(1); // exit zoom-out
  const logoOpacity = useSharedValue(0);
  const logoScale = useSharedValue(0.7);
  const glow = useSharedValue(0); // breathing halo
  const ring = useSharedValue(0); // radar pulse 0..1
  const shine = useSharedValue(0); // metallic sweep 0..1
  const word = useSharedValue(0); // wordmark reveal
  const bar = useSharedValue(0); // loading bar sweep 0..1

  // Intro choreography (runs once on mount)
  useEffect(() => {
    logoOpacity.value = withTiming(1, { duration: 420, easing: Easing.out(Easing.cubic) });
    logoScale.value = withSpring(1, { damping: 11, stiffness: 120, mass: 0.9 });

    glow.value = withDelay(
      160,
      withRepeat(
        withSequence(
          withTiming(1, { duration: 1100, easing: Easing.inOut(Easing.quad) }),
          withTiming(0.55, { duration: 1100, easing: Easing.inOut(Easing.quad) }),
        ),
        -1,
        true,
      ),
    );

    ring.value = withDelay(
      420,
      withRepeat(withTiming(1, { duration: 1900, easing: Easing.out(Easing.cubic) }), -1, false),
    );

    shine.value = withDelay(
      680,
      withRepeat(
        withSequence(
          withTiming(1, { duration: 1050, easing: Easing.inOut(Easing.cubic) }),
          withDelay(1500, withTiming(1, { duration: 0 })),
          withTiming(0, { duration: 0 }),
        ),
        -1,
        false,
      ),
    );

    word.value = withDelay(560, withTiming(1, { duration: 620, easing: Easing.out(Easing.cubic) }));

    bar.value = withDelay(
      520,
      withRepeat(withTiming(1, { duration: 1250, easing: Easing.inOut(Easing.cubic) }), -1, false),
    );

    const t1 = setTimeout(() => setMinElapsed(true), minDuration);
    const t2 = setTimeout(() => setForced(true), maxDuration);
    return () => {
      clearTimeout(t1);
      clearTimeout(t2);
      cancelAnimation(glow);
      cancelAnimation(ring);
      cancelAnimation(shine);
      cancelAnimation(bar);
    };
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  // Exit when the app is ready (and min time passed) or the hard cap is hit
  useEffect(() => {
    if (exiting.current) return;
    if ((appReady && minElapsed) || forced) {
      exiting.current = true;
      cancelAnimation(glow);
      cancelAnimation(ring);
      cancelAnimation(shine);
      cancelAnimation(bar);
      rootScale.value = withTiming(1.08, { duration: 520, easing: Easing.in(Easing.cubic) });
      root.value = withDelay(
        80,
        withTiming(0, { duration: 460, easing: Easing.in(Easing.cubic) }, (done) => {
          if (done) runOnJS(onFinish)();
        }),
      );
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [appReady, minElapsed, forced]);

  // Animated styles
  const rootStyle = useAnimatedStyle(() => ({
    opacity: root.value,
    transform: [{ scale: rootScale.value }],
  }));

  const logoStyle = useAnimatedStyle(() => ({
    opacity: logoOpacity.value,
    transform: [{ scale: logoScale.value }],
  }));

  const glowStyle = useAnimatedStyle(() => ({
    opacity: interpolate(glow.value, [0, 1], [0.12, 0.42]),
    transform: [{ scale: interpolate(glow.value, [0, 1], [0.9, 1.12]) }],
  }));

  const ringStyle = useAnimatedStyle(() => ({
    opacity: interpolate(ring.value, [0, 0.15, 1], [0, 0.5, 0]),
    transform: [{ scale: interpolate(ring.value, [0, 1], [0.85, 1.9]) }],
  }));

  const ring2Style = useAnimatedStyle(() => {
    const v = (ring.value + 0.5) % 1;
    return {
      opacity: interpolate(v, [0, 0.15, 1], [0, 0.35, 0]),
      transform: [{ scale: interpolate(v, [0, 1], [0.85, 1.9]) }],
    };
  });

  const shineStyle = useAnimatedStyle(() => ({
    opacity: interpolate(shine.value, [0, 0.1, 0.85, 1], [0, 0.85, 0.85, 0]),
    transform: [
      { translateX: interpolate(shine.value, [0, 1], [-LOGO_SIZE * 0.9, LOGO_SIZE * 0.9]) },
      { rotate: "18deg" },
    ],
  }));

  const wordStyle = useAnimatedStyle(() => ({
    opacity: word.value,
    transform: [{ translateY: interpolate(word.value, [0, 1], [14, 0]) }],
  }));

  const barFillStyle = useAnimatedStyle(() => ({
    transform: [{ translateX: interpolate(bar.value, [0, 1], [-BAR_W, BAR_W]) }],
  }));

  return (
    <Animated.View style={[StyleSheet.absoluteFill, styles.root, rootStyle]} pointerEvents="auto">
      <LinearGradient
        colors={["#0A0A0A", "#000000", "#070707"]}
        locations={[0, 0.55, 1]}
        style={StyleSheet.absoluteFill}
      />

      <View style={styles.center}>
        <View style={styles.logoZone}>
          {/* Soft Yowell-blue halo */}
          <Animated.View style={[styles.glow, glowStyle]} />
          {/* Radar pulse rings */}
          <Animated.View style={[styles.pulseRing, ringStyle]} />
          <Animated.View style={[styles.pulseRing, ring2Style]} />

          {/* Logo with metallic sweep clipped to its bounds */}
          <View style={styles.logoClip}>
            <Animated.Image source={logo} style={[styles.logo, logoStyle]} resizeMode="contain" />
            <Animated.View style={[styles.shine, shineStyle]}>
              <LinearGradient
                colors={["transparent", "rgba(255,255,255,0.55)", "transparent"]}
                start={{ x: 0, y: 0 }}
                end={{ x: 1, y: 0 }}
                style={StyleSheet.absoluteFill}
              />
            </Animated.View>
          </View>
        </View>

        <Animated.View style={[styles.wordWrap, wordStyle]}>
           <Text style={styles.brand}>YOWELL RENT-CAR</Text>
           <Text style={styles.tagline}>Alquiler de vehículos</Text>
        </Animated.View>

        <View style={styles.barTrack}>
          <Animated.View style={[styles.barFill, barFillStyle]}>
            <LinearGradient
              colors={["transparent", colors.primary, colors.primaryLight, colors.primary, "transparent"]}
              start={{ x: 0, y: 0 }}
              end={{ x: 1, y: 0 }}
              style={StyleSheet.absoluteFill}
            />
          </Animated.View>
        </View>
      </View>
    </Animated.View>
  );
}

const BAR_TRACK_W = 150;
const BAR_W = BAR_TRACK_W * 0.6;

const styles = StyleSheet.create({
  root: {
    zIndex: 999,
    elevation: 999,
    backgroundColor: "#000000",
  },
  center: {
    flex: 1,
    alignItems: "center",
    justifyContent: "center",
    paddingBottom: SCREEN_W > 0 ? 8 : 0,
  },
  logoZone: {
    width: LOGO_SIZE + 80,
    height: LOGO_SIZE + 80,
    alignItems: "center",
    justifyContent: "center",
  },
  glow: {
    position: "absolute",
    width: LOGO_SIZE + 40,
    height: LOGO_SIZE + 40,
    borderRadius: (LOGO_SIZE + 40) / 2,
    backgroundColor: colors.primary,
    shadowColor: colors.primary,
    shadowOffset: { width: 0, height: 0 },
    shadowOpacity: 0.9,
    shadowRadius: 60,
    elevation: 24,
  },
  pulseRing: {
    position: "absolute",
    width: LOGO_SIZE,
    height: LOGO_SIZE,
    borderRadius: LOGO_SIZE / 2,
    borderWidth: 1.5,
    borderColor: colors.primary,
  },
  logoClip: {
    width: LOGO_SIZE,
    height: LOGO_SIZE,
    overflow: "hidden",
    alignItems: "center",
    justifyContent: "center",
  },
  logo: {
    width: LOGO_SIZE,
    height: LOGO_SIZE,
  },
  shine: {
    position: "absolute",
    top: -LOGO_SIZE * 0.3,
    bottom: -LOGO_SIZE * 0.3,
    width: LOGO_SIZE * 0.32,
  },
  wordWrap: {
    marginTop: 26,
    alignItems: "center",
  },
  brand: {
    fontFamily: font.extrabold,
    fontSize: 19,
    letterSpacing: 3.5,
    color: "#FFFFFF",
  },
  tagline: {
    marginTop: 8,
    fontFamily: font.medium,
    fontSize: 12,
    letterSpacing: 2,
    textTransform: "uppercase",
    color: colors.primary,
  },
  barTrack: {
    marginTop: 34,
    width: BAR_TRACK_W,
    height: 3,
    borderRadius: 3,
     backgroundColor: "rgba(24,40,232,0.22)",
    overflow: "hidden",
  },
  barFill: {
    width: BAR_W,
    height: "100%",
  },
});
