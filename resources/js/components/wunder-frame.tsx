import { useEffect, useRef } from 'react';

type WunderFrameProps = {
    children: React.ReactNode;
    className?: string;
    contentClassName?: string;
};

/**
 * Wundernn.io-style frame wrapper.
 * Outer page is the signature warm cream border (#FFF9D9).
 * Inner container is rounded with zero black border.
 * The video stays fixed in the background with a comfortable dark glass tint (not overly bright).
 * The content scrolls with hidden scrollbars (no-scrollbar).
 */
export function WunderFrame({
    children,
    className = '',
    contentClassName = '',
}: WunderFrameProps) {
    const videoRef = useRef<HTMLVideoElement>(null);

    useEffect(() => {
        const video = videoRef.current;
        if (!video) return;

        video.muted = true;
        const playPromise = video.play();
        if (playPromise !== undefined) {
            playPromise.catch(() => {
                // Autoplay was prevented; fallback remains poster
            });
        }
    }, []);

    return (
        <div className="fixed inset-0 h-screen w-screen overflow-hidden bg-[#FFF9D9] p-2 select-none sm:p-2.5 md:p-3">
            {/* Inner Video Container: Rounded with zero black border */}
            <div
                className={`relative h-full w-full overflow-hidden rounded-[20px] bg-[#0f1712] md:rounded-[24px] ${className}`}
            >
                {/* Background Video & Poster: Comfortable dark glass tone (eliminated harsh glare) */}
                <div className="pointer-events-none absolute inset-0 z-0 h-full w-full overflow-hidden select-none">
                    <img
                        src="/backgrounds/clear_glass_poster.webp"
                        alt=""
                        className="h-full w-full object-cover"
                        style={{
                            filter: 'brightness(0.58) contrast(1.1) saturate(1.15)',
                        }}
                    />
                    <video
                        ref={videoRef}
                        src="/backgrounds/clear_glass_web.mp4"
                        poster="/backgrounds/clear_glass_poster.webp"
                        autoPlay
                        muted
                        loop
                        playsInline
                        preload="auto"
                        className="absolute inset-0 h-full w-full object-cover"
                        style={{
                            filter: 'brightness(0.58) contrast(1.1) saturate(1.15)',
                        }}
                    />

                    {/* Dark tinted glass scrim so video is clearly visible without being overly bright */}
                    <div className="absolute inset-0 bg-black/35" />
                    <div className="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-black/35" />
                </div>

                {/* Content Layer: Scrolling smoothly with NO scrollbar */}
                <div
                    className={`relative z-10 h-full w-full [scrollbar-width:none] overflow-x-hidden overflow-y-auto scroll-smooth [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden ${contentClassName}`}
                >
                    {children}
                </div>
            </div>
        </div>
    );
}
