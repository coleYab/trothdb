import { Link } from '@inertiajs/react';
import { Palette, ShieldCheck, UserRound } from 'lucide-react';
import type { PropsWithChildren } from 'react';
import { PageHeader } from '@/components/hub-ui';
import { useCurrentUrl } from '@/hooks/use-current-url';
import { cn, toUrl } from '@/lib/utils';
import { edit as editAppearance } from '@/routes/appearance';
import { edit } from '@/routes/profile';
import { edit as editSecurity } from '@/routes/security';
import type { NavItem } from '@/types';

const sidebarNavItems: NavItem[] = [
    {
        title: 'Profile',
        href: edit(),
        icon: UserRound,
    },
    {
        title: 'Security',
        href: editSecurity(),
        icon: ShieldCheck,
    },
    {
        title: 'Appearance',
        href: editAppearance(),
        icon: Palette,
    },
];

export default function SettingsLayout({ children }: PropsWithChildren) {
    const { isCurrentOrParentUrl } = useCurrentUrl();

    return (
        <div className="mx-auto w-full max-w-6xl flex-1 p-4 md:p-8">
            <PageHeader
                eyebrow="Account"
                title="Settings"
                description="Identity, credentials, devices, and appearance for this account."
            />

            <div className="mt-8 flex flex-col gap-8 lg:flex-row lg:gap-12">
                <aside className="w-full lg:w-44 lg:shrink-0">
                    <nav
                        className="flex gap-1 overflow-x-auto border-b border-border/80 pb-2 lg:sticky lg:top-20 lg:flex-col lg:border-0 lg:pb-0"
                        aria-label="Settings"
                    >
                        {sidebarNavItems.map((item, index) => {
                            const active = isCurrentOrParentUrl(item.href);

                            return (
                                <Link
                                    key={`${toUrl(item.href)}-${index}`}
                                    href={item.href}
                                    className={cn(
                                        'flex shrink-0 items-center gap-2 rounded-sm px-2.5 py-1.5 font-mono text-[11px] transition-colors',
                                        active
                                            ? 'bg-foreground text-background'
                                            : 'text-muted-foreground hover:bg-muted/60 hover:text-foreground',
                                    )}
                                >
                                    {item.icon && (
                                        <item.icon className="size-3.5" />
                                    )}
                                    {item.title}
                                </Link>
                            );
                        })}
                    </nav>
                </aside>

                <div className="min-w-0 flex-1">
                    <section className="max-w-2xl space-y-10">
                        {children}
                    </section>
                </div>
            </div>
        </div>
    );
}
