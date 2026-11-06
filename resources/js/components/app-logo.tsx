import { usePage } from '@inertiajs/react';

export default function AppLogo() {
    const { name } = usePage().props;
    void name;

    return (
        <>
            <span className="flex aspect-square size-8 shrink-0 items-center justify-center rounded-sm bg-foreground font-mono text-[11px] font-semibold tracking-tighter text-background">
                {'{}'}
            </span>
            <span className="ml-2 grid flex-1 text-left leading-none group-data-[collapsible=icon]:hidden">
                <span className="display-title text-[1.35rem] tracking-tight">
                    learn<span className="text-emerald-700 dark:text-emerald-400">CP</span>
                </span>
                <span className="mt-1 font-mono text-[9px] tracking-[0.18em] text-muted-foreground uppercase">
                    C++ judge
                </span>
            </span>
        </>
    );
}
