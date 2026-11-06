import { Link } from '@inertiajs/react';
import { Code2, GraduationCap, LayoutGrid, Plus, Terminal, Wrench } from 'lucide-react';
import AppLogo from '@/components/app-logo';
import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { index as learn } from '@/routes/learn';
import { index as problemset } from '@/routes/problemset';
import { index as setup } from '@/routes/setup';

const navGroups = [
    {
        label: 'Solve',
        items: [
            {
                title: 'Today',
                href: dashboard(),
                icon: LayoutGrid,
            },
            {
                title: 'Problemset',
                href: problemset(),
                icon: Code2,
            },
            {
                title: 'New problem',
                href: '/problems/create',
                icon: Plus,
            },
        ],
    },
    {
        label: 'Study',
        items: [
            {
                title: 'Tracks',
                href: learn(),
                icon: GraduationCap,
            },
            {
                title: 'Local setup',
                href: setup(),
                icon: Wrench,
            },
        ],
    },
];

const footerNavItems: never[] = [];

export function AppSidebar() {
    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={dashboard()} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain groups={navGroups} />
            </SidebarContent>

            <SidebarFooter>
                <div className="mb-2 hidden rounded-md border border-sidebar-border bg-background/50 p-2.5 group-data-[collapsible=icon]:hidden group-data-[collapsible=icon]:p-0">
                    <div className="flex items-center gap-2">
                        <span className="relative flex size-1.5">
                            <span className="absolute inline-flex size-1.5 rounded-full bg-emerald-500" />
                        </span>
                        <span className="font-mono text-[10px] font-medium tracking-wider text-foreground uppercase">
                            Judge ready
                        </span>
                        <Terminal className="ml-auto size-3 text-muted-foreground" />
                    </div>
                    <p className="mt-1.5 font-mono text-[10px] leading-4 text-muted-foreground">
                        GCC 14 · C++20 · ASan
                    </p>
                    <Link
                        href={setup()}
                        className="mt-2 inline-flex font-mono text-[10px] text-emerald-700 underline-offset-4 hover:underline dark:text-emerald-400"
                    >
                        arena doctor →
                    </Link>
                </div>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
