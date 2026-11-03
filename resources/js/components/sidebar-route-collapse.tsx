import { usePage } from '@inertiajs/react';
import { useEffect, useRef } from 'react';
import { useSidebar } from '@/components/ui/sidebar';

const problemDetailPattern = /^\/problems\/[^/]+/;
const collapsedByProblemPageKey = 'problem-sidebar-collapsed';

/**
 * Collapses the sidebar when a problem detail page is opened, then restores
 * the previous state once the user navigates back to a regular page.
 *
 * The flag lives in session storage so it survives the layout remount that
 * happens during Inertia navigations, while the ref keeps manual expansion
 * on the same page possible.
 */
export function SidebarRouteCollapse() {
    const { url } = usePage();
    const { open, setOpen, isMobile } = useSidebar();

    const path = url.split('?')[0];
    const isProblemPage = problemDetailPattern.test(path);
    const lastCollapsedPath = useRef<string | null>(null);

    useEffect(() => {
        if (isMobile) {
            return;
        }

        if (isProblemPage) {
            if (lastCollapsedPath.current === path) {
                return;
            }

            lastCollapsedPath.current = path;

            if (open) {
                sessionStorage.setItem(collapsedByProblemPageKey, '1');
                setOpen(false);
            }

            return;
        }

        lastCollapsedPath.current = null;

        if (sessionStorage.getItem(collapsedByProblemPageKey) === '1') {
            sessionStorage.removeItem(collapsedByProblemPageKey);
            setOpen(true);
        }
    }, [path, isProblemPage, isMobile, open, setOpen]);

    return null;
}
