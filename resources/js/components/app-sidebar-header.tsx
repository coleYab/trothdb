import { Link } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import { Breadcrumbs } from '@/components/breadcrumbs';
import { ThemeToggle } from '@/components/theme-toggle';
import { Button } from '@/components/ui/button';
import { SidebarTrigger } from '@/components/ui/sidebar';
import type { BreadcrumbItem as BreadcrumbItemType } from '@/types';

export function AppSidebarHeader({
    breadcrumbs = [],
}: {
    breadcrumbs?: BreadcrumbItemType[];
}) {
    return (
        <header className="sticky top-0 z-20 flex h-14 shrink-0 items-center gap-2 border-b border-border/80 bg-background/85 px-4 backdrop-blur transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-6">
            <div className="flex min-w-0 items-center gap-2">
                <SidebarTrigger className="-ml-1" />
                <span className="h-4 w-px bg-border" aria-hidden="true" />
                <Breadcrumbs breadcrumbs={breadcrumbs} />
            </div>

            <div className="ml-auto flex items-center gap-1.5">
                <span className="hidden items-center gap-1.5 rounded-sm border border-border/80 bg-card px-2 py-1 font-mono text-[10px] tracking-wider text-muted-foreground uppercase sm:inline-flex">
                    <span className="size-1.5 rounded-full bg-emerald-500" />
                    C++20
                </span>
                <Button
                    variant="ghost"
                    size="sm"
                    asChild
                    className="hidden h-8 gap-1.5 text-xs text-muted-foreground hover:text-foreground sm:inline-flex"
                >
                    <Link href="/problems/create">
                        <Plus className="size-3.5" />
                        New problem
                    </Link>
                </Button>
                <ThemeToggle />
            </div>
        </header>
    );
}
