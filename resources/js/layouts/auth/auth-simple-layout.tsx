import { WunderFrame } from '@/components/wunder-frame';
import type { AuthLayoutProps } from '@/types';

export default function AuthSimpleLayout({
    children,
    title,
    description,
}: AuthLayoutProps) {
    return (
        <WunderFrame contentClassName="flex flex-col items-center justify-center p-3 sm:p-5">
            <div className="my-auto w-full max-w-md py-1">
                {/* Glassmorphic Auth Card: Centered with compact spacing and crystal-clear contrast */}
                <main className="rounded-2xl border border-white/15 bg-black/45 p-5 text-[#FFF9D9] shadow-2xl ring-1 ring-white/10 backdrop-blur-xl sm:p-6 [&_[data-slot=button]:not([type=submit])]:border-white/20 [&_[data-slot=button]:not([type=submit])]:bg-black/30 [&_[data-slot=button]:not([type=submit])]:!text-[#FFF9D9] [&_[data-slot=button]:not([type=submit])]:hover:bg-black/50 [&_a]:text-[#FFF9D9] [&_a]:hover:underline [&_button:not([type=submit])]:!text-[#FFF9D9] [&_button[type=submit]]:bg-[#FFF9D9] [&_button[type=submit]]:font-mono [&_button[type=submit]]:font-semibold [&_button[type=submit]]:text-slate-950 [&_button[type=submit]]:hover:bg-white [&_input]:border-white/25 [&_input]:bg-black/30 [&_input]:text-[#FFF9D9] [&_input]:placeholder:text-[#FFF9D9]/40 [&_label]:font-mono [&_label]:text-xs [&_label]:text-[#FFF9D9] [&_span.bg-background]:!bg-transparent [&_span.bg-background]:!text-[#FFF9D9]/70">
                    <div className="flex flex-col gap-4">
                        <div className="space-y-1 text-center">
                            <h1 className="font-['Redaction_50',serif] text-2xl font-normal tracking-tight text-[#FFF9D9] [text-shadow:0_0_12px_rgba(255,249,217,0.3)] sm:text-3xl">
                                {title}
                            </h1>
                            {description && (
                                <p className="font-mono text-xs text-[#FFF9D9]/75">
                                    {description}
                                </p>
                            )}
                        </div>

                        <div>{children}</div>
                    </div>
                </main>
            </div>
        </WunderFrame>
    );
}
