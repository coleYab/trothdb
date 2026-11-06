import { Head, Link } from '@inertiajs/react';
import {
    CheckCircle2,
    Cpu,
    ExternalLink,
    Lock,
    Shield,
    Terminal,
    XCircle,
    Zap,
} from 'lucide-react';
import { useEffect, useState } from 'react';
import AppLogo from '@/components/app-logo';
import { Button } from '@/components/ui/button';
import { dashboard } from '@/routes';

type AuthRequest = {
    user_code: string;
    device_code: string;
    client_name: string;
    callback_port: number | null;
    status: 'pending' | 'approved' | 'denied' | 'expired';
    expires_at: string;
};

type UserInfo = {
    name: string;
    username: string | null;
    email: string;
};

export default function CliAuthorize({
    authRequest,
    user,
}: {
    authRequest: AuthRequest | null;
    user: UserInfo;
}) {
    const [status, setStatus] = useState<
        'pending' | 'approved' | 'denied' | 'expired' | 'invalid'
    >(() => {
        if (!authRequest) {
            return 'invalid';
        }
        return authRequest.status;
    });

    const [submitting, setSubmitting] = useState(false);
    const [token, setToken] = useState<string | null>(null);

    const clientName = authRequest?.client_name || 'LocalJudge';
    const userCode = authRequest?.user_code || '—';

    // If already approved with callback port, attempt background loopback notify
    useEffect(() => {
        if (status === 'approved' && authRequest?.callback_port && token) {
            const loopbackUrl = `http://127.0.0.1:${authRequest.callback_port}/callback?token=${token}&user=${user.username || user.name}`;
            fetch(loopbackUrl, { mode: 'no-cors' }).catch(() => {
                // Ignore loopback network issues in browser
            });
        }
    }, [status, authRequest?.callback_port, token, user.name, user.username]);

    const handleApprove = async () => {
        if (!authRequest) return;
        setSubmitting(true);

        try {
            const res = await fetch('/cli/authorize/approve', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    device_code: authRequest.device_code,
                }),
            });

            if (res.ok) {
                const data = await res.json();
                setToken(data.token);
                setStatus('approved');
            } else {
                setStatus('expired');
            }
        } catch {
            setStatus('approved');
        } finally {
            setSubmitting(false);
        }
    };

    const handleDeny = async () => {
        if (!authRequest) return;
        setSubmitting(true);

        try {
            await fetch('/cli/authorize/deny', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    device_code: authRequest.device_code,
                }),
            });
            setStatus('denied');
        } catch {
            setStatus('denied');
        } finally {
            setSubmitting(false);
        }
    };

    return (
        <>
            <Head title={`Authorize ${clientName}`} />

            <div className="paper-grid flex min-h-svh w-full items-center justify-center bg-background p-4">
                <div className="w-full max-w-md overflow-hidden rounded-lg border border-border/90 bg-card">
                    {/* Brand header */}
                    <div className="flex items-center justify-between border-b border-border/80 bg-muted/30 px-5 py-3">
                        <Link
                            href={dashboard()}
                            className="flex items-center gap-2"
                        >
                            <AppLogo />
                        </Link>
                        <span className="inline-flex items-center gap-1.5 rounded-sm border border-border/80 bg-background px-2 py-0.5 font-mono text-[10px] tracking-wider text-muted-foreground uppercase">
                            <Terminal className="size-3" />
                            CLI auth
                        </span>
                    </div>

                    {/* Pending Authorization State */}
                    {status === 'pending' && (
                        <div className="space-y-5 p-5 sm:p-6">
                            <div className="space-y-2">
                                <p className="meta-label">Device request</p>
                                <h1 className="display-title text-3xl text-foreground">
                                    Authorize {clientName}
                                </h1>
                                <p className="text-xs leading-5 text-muted-foreground">
                                    A local terminal session is requesting
                                    permission to connect to your learnCP
                                    account.
                                </p>
                            </div>

                            {/* Verification Code Box */}
                            <div className="rounded-md border border-border/90 bg-background p-4 text-center">
                                <span className="font-mono text-[10px] tracking-[0.16em] text-muted-foreground uppercase">
                                    Confirmation code
                                </span>
                                <p className="mt-1 font-mono text-2xl font-semibold tracking-[0.2em] text-foreground">
                                    {userCode}
                                </p>
                                <p className="mt-1 text-[11px] text-muted-foreground">
                                    Verify this code matches the prompt in your
                                    terminal.
                                </p>
                            </div>

                            {/* Account and permissions */}
                            <div className="space-y-3 rounded-md border border-border/80 p-4 text-xs">
                                <div className="flex items-center justify-between border-b border-border/60 pb-2.5">
                                    <span className="text-muted-foreground">
                                        Account
                                    </span>
                                    <span className="font-mono font-medium text-foreground">
                                        {user.username || user.name}
                                    </span>
                                </div>

                                <div className="space-y-2 pt-1">
                                    <span className="meta-label">
                                        Permissions granted
                                    </span>
                                    <ul className="space-y-2 text-muted-foreground">
                                        <li className="flex items-start gap-2">
                                            <Zap className="mt-0.5 size-3.5 shrink-0 text-emerald-600 dark:text-emerald-400" />
                                            <span>
                                                Fetch problems, test suites, and
                                                starter code
                                            </span>
                                        </li>
                                        <li className="flex items-start gap-2">
                                            <Cpu className="mt-0.5 size-3.5 shrink-0 text-muted-foreground" />
                                            <span>
                                                Run local benchmark tests and
                                                compiler sanitizers
                                            </span>
                                        </li>
                                        <li className="flex items-start gap-2">
                                            <Shield className="mt-0.5 size-3.5 shrink-0 text-emerald-600 dark:text-emerald-400" />
                                            <span>
                                                Submit verified solutions and
                                                record metrics
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            {/* Action Buttons */}
                            <div className="flex flex-col gap-2">
                                <Button
                                    type="button"
                                    onClick={handleApprove}
                                    disabled={submitting}
                                    className="w-full"
                                >
                                    {submitting
                                        ? 'Authorizing...'
                                        : `Authorize ${clientName}`}
                                </Button>
                                <Button
                                    type="button"
                                    variant="outline"
                                    onClick={handleDeny}
                                    disabled={submitting}
                                    className="w-full bg-transparent"
                                >
                                    Cancel &amp; deny
                                </Button>
                            </div>
                        </div>
                    )}

                    {/* Approved Success State */}
                    {status === 'approved' && (
                        <div className="space-y-5 p-5 text-center sm:p-6">
                            <div className="mx-auto flex size-11 items-center justify-center rounded-sm border border-emerald-600/30 bg-emerald-600/10 text-emerald-700 dark:text-emerald-400">
                                <CheckCircle2 className="size-5" />
                            </div>

                            <div className="space-y-1.5">
                                <h2 className="display-title text-3xl text-foreground">
                                    {clientName} connected
                                </h2>
                                <p className="text-xs leading-5 text-muted-foreground">
                                    Your device authorization was successful.
                                    You can close this window and return to your
                                    terminal.
                                </p>
                            </div>

                            <div className="rounded-md border border-emerald-600/25 bg-emerald-600/5 p-4 text-xs">
                                <p className="font-mono font-medium">
                                    Session active for{' '}
                                    {user.username || user.name}
                                </p>
                                <p className="mt-1 text-[11px] opacity-80">
                                    The CLI is now authorized to run tests and
                                    submit solutions.
                                </p>
                            </div>

                            <Button
                                variant="outline"
                                className="w-full bg-transparent"
                                asChild
                            >
                                <Link href={dashboard()}>
                                    Go to dashboard
                                    <ExternalLink className="size-3.5" />
                                </Link>
                            </Button>
                        </div>
                    )}

                    {/* Denied State */}
                    {status === 'denied' && (
                        <div className="space-y-5 p-5 text-center sm:p-6">
                            <div className="mx-auto flex size-11 items-center justify-center rounded-sm border border-border/80 bg-muted/40 text-muted-foreground">
                                <XCircle className="size-5" />
                            </div>

                            <div className="space-y-1.5">
                                <h2 className="display-title text-3xl text-foreground">
                                    Connection denied
                                </h2>
                                <p className="text-xs leading-5 text-muted-foreground">
                                    You declined the authorization request. No
                                    access token was issued to {clientName}.
                                </p>
                            </div>

                            <Button
                                variant="outline"
                                className="w-full bg-transparent"
                                asChild
                            >
                                <Link href={dashboard()}>
                                    Back to dashboard
                                </Link>
                            </Button>
                        </div>
                    )}

                    {/* Expired / Invalid State */}
                    {(status === 'expired' || status === 'invalid') && (
                        <div className="space-y-5 p-5 text-center sm:p-6">
                            <div className="mx-auto flex size-11 items-center justify-center rounded-sm border border-amber-600/30 bg-amber-600/10 text-amber-700 dark:text-amber-400">
                                <Lock className="size-5" />
                            </div>

                            <div className="space-y-1.5">
                                <h2 className="display-title text-3xl text-foreground">
                                    Request expired
                                </h2>
                                <p className="text-xs leading-5 text-muted-foreground">
                                    This device authorization link is no longer
                                    valid. Run the login command in your
                                    terminal again to start a new session.
                                </p>
                            </div>

                            <Button
                                variant="outline"
                                className="w-full bg-transparent"
                                asChild
                            >
                                <Link href={dashboard()}>
                                    Back to dashboard
                                </Link>
                            </Button>
                        </div>
                    )}
                </div>
            </div>
        </>
    );
}
