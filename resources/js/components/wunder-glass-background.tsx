import { useEffect, useRef, useState } from 'react';

type WunderGlassBackgroundProps = {
    className?: string;
    intensity?: 'normal' | 'darker' | 'subtle';
};

/**
 * Wundernn.io-style clear glass video and poster background with bezel framing.
 * Replaces the procedural canvas with the authentic looping glass fluid video
 * and high-resolution poster fallback.
 */
export function WunderGlassBackground({
    className = '',
    intensity = 'normal',
}: WunderGlassBackgroundProps) {
    const videoRef = useRef<HTMLVideoElement>(null);
    const [isVideoLoaded, setIsVideoLoaded] = useState(false);

    useEffect(() => {
        const video = videoRef.current;
        if (!video) return;

        // Ensure autoplay reliably starts
        video.muted = true;
        const playPromise = video.play();
        if (playPromise !== undefined) {
            playPromise.catch(() => {
                // Autoplay was prevented; fallback remains poster
            });
        }
    }, []);

    const overlayClass =
        intensity === 'darker'
            ? 'bg-black/60'
            : intensity === 'subtle'
              ? 'bg-black/30'
              : 'bg-black/45';

    return (
        <div
            className={`pointer-events-none fixed inset-0 z-0 select-none ${className}`}
            aria-hidden="true"
        >
            {/* Outer bezel frame (Wundernn signature outer border) */}
            <div className="absolute inset-0 bg-[#FFF9D9] transition-colors duration-500 dark:bg-[#0b120e]" />

            {/* Inset rounded container with glass video */}
            <div className="fixed inset-2 z-0 overflow-hidden rounded-[20px] bg-[#16231c] shadow-2xl ring-1 ring-black/20 md:inset-3 md:rounded-[24px] dark:ring-white/10">
                {/* Poster fallback image */}
                <img
                    src="/backgrounds/clear_glass_poster.webp"
                    alt=""
                    className={`absolute inset-0 h-full w-full object-cover transition-opacity duration-1000 ${
                        isVideoLoaded ? 'opacity-0' : 'opacity-100'
                    }`}
                    style={{
                        filter: 'brightness(0.7) contrast(1.05) saturate(1.1)',
                    }}
                />

                {/* Looping glass video */}
                <video
                    ref={videoRef}
                    src="/backgrounds/clear_glass_web.mp4"
                    poster="/backgrounds/clear_glass_poster.webp"
                    autoPlay
                    muted
                    loop
                    playsInline
                    preload="auto"
                    onCanPlay={() => setIsVideoLoaded(true)}
                    className="absolute inset-0 h-full w-full object-cover transition-opacity duration-1000"
                    style={{
                        filter: 'brightness(0.68) contrast(1.08) saturate(1.15)',
                    }}
                />

                {/* Shading, vignette & dark backdrop overlays for crystal-clear text legibility */}
                <div
                    className={`absolute inset-0 ${overlayClass} backdrop-blur-[2px] transition-colors duration-500`}
                />
                <div className="absolute inset-0 bg-gradient-to-t from-black/85 via-black/20 to-black/55" />
                <div className="absolute inset-0 bg-[radial-gradient(ellipse_at_center,transparent_20%,rgba(0,0,0,0.7)_100%)]" />

                {/* Bezel inner border ring */}
                <div className="absolute inset-0 rounded-[20px] ring-1 ring-white/10 md:rounded-[24px]" />
            </div>
        </div>
    );
}
