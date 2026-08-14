/**
 * TabBarScrollContext
 * Provides an `onScroll` handler that screens attach to their FlatList/ScrollView.
 * The layout reads `translateY` to animate the tab bar off the bottom edge.
 */
import React, { createContext, useCallback, useContext, useRef } from "react";
import { Animated, Platform } from "react-native";

export const TAB_BAR_HEIGHT = Platform.OS === "web" ? 84 : 88;
const TAB_H = TAB_BAR_HEIGHT;
const THRESHOLD = 60; // don't hide until user has scrolled past this
const DY = 5;         // minimum delta to trigger hide/show

interface TabBarScrollCtx {
  translateY: Animated.Value;
  onScroll: (e: { nativeEvent: { contentOffset: { y: number } } }) => void;
  showTabBar: () => void;
}

const TabBarScrollContext = createContext<TabBarScrollCtx>({
  translateY: new Animated.Value(0),
  onScroll: () => {},
  showTabBar: () => {},
});

export const useTabBarScroll = () => useContext(TabBarScrollContext);

export function TabBarScrollProvider({ children }: { children: React.ReactNode }) {
  const translateY = useRef(new Animated.Value(0)).current;
  const lastY = useRef(0);
  const isHidden = useRef(false);

  const animate = useCallback(
    (toValue: number) => {
      Animated.spring(translateY, {
        toValue,
        useNativeDriver: true,
        bounciness: 0,
        speed: 20,
      }).start();
    },
    [translateY]
  );

  const showTabBar = useCallback(() => {
    if (isHidden.current) {
      isHidden.current = false;
      animate(0);
    }
  }, [animate]);

  const onScroll = useCallback(
    (e: { nativeEvent: { contentOffset: { y: number } } }) => {
      const y = e.nativeEvent.contentOffset.y;
      const dy = y - lastY.current;
      lastY.current = y;

      if (dy > DY && !isHidden.current && y > THRESHOLD) {
        isHidden.current = true;
        animate(TAB_H);
      } else if (dy < -DY && isHidden.current) {
        isHidden.current = false;
        animate(0);
      }
    },
    [animate]
  );

  return (
    <TabBarScrollContext.Provider value={{ translateY, onScroll, showTabBar }}>
      {children}
    </TabBarScrollContext.Provider>
  );
}
