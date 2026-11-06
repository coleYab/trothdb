import { Head, Link, usePage } from '@inertiajs/react';
import {
    AlertTriangle,
    Apple,
    CheckCircle2,
    ChevronRight,
    CircleHelp,
    Command,
    Cpu,
    Download,
    KeyRound,
    Laptop,
    Lock,
    Monitor,
    PlugZap,
    RefreshCw,
    Search,
    ShieldCheck,
    Terminal,
    Trash2,
    Users,
    XCircle,
} from 'lucide-react';
import { useMemo, useState } from 'react';
import { toast } from 'sonner';
import { Callout } from '@/components/callout';
import {
    CodeBlock,
    EmptyState,
    ErrorState,
    LoadingState,
    PageHeader,
    Pill,
    SectionCard,
    SegmentedTabs,
    StatTile,
} from '@/components/hub-ui';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { edit as securityRoute } from '@/routes/security';
import { cn } from '@/lib/utils';

type OperatingSystem = 'macos' | 'linux' | 'windows';
type CheckStatus = 'pass' | 'warn' | 'fail';
type DoctorState = 'ready' | 'running';

type InstallStep = {
    title: string;
    description: string;
    command: string;
};

type Session = {
    id: string;
    device: string;
    os: string;
    location: string;
    lastUsed: string;
    token: string;
    current: boolean;
};

const operatingSystems: Array<{
    value: OperatingSystem;
    label: string;
    icon: typeof Apple;
}> = [
    { value: 'macos', label: 'macOS', icon: Apple },
    { value: 'linux', label: 'Linux', icon: Terminal },
    { value: 'windows', label: 'Windows', icon: Monitor },
];

const installSteps: Record<OperatingSystem, InstallStep[]> = {
    macos: [
        {
            title: 'Install the Apple command-line tools',
            description:
                'This provides clang, make, and git. Accept the licence prompt if it appears.',
            command: 'xcode-select --install',
        },
        {
            title: 'Install CMake and a modern GCC',
            description:
                'Homebrew keeps the toolchain separate from the system clang. The CLI prefers GCC 14 when it is available.',
            command: 'brew install cmake git gcc',
        },
        {
            title: 'Install the arena CLI',
            description:
                'The installer writes to ~/.local/bin. Add that directory to PATH if the shell cannot find arena afterwards.',
            command: 'curl -fsSL https://arena.dev/install.sh | sh',
        },
        {
            title: 'Authenticate the device',
            description:
                'The login command opens a browser session and stores a scoped token in the system keychain.',
            command: 'arena auth login',
        },
        {
            title: 'Verify the installation',
            description:
                'Doctor checks the compiler, CMake, Git, network access, and the sandbox connection.',
            command: 'arena doctor',
        },
    ],
    linux: [
        {
            title: 'Install the build essentials',
            description:
                'Debian and Ubuntu use apt. On Fedora, use dnf groupinstall "Development Tools" and install cmake and git.',
            command:
                'sudo apt update && sudo apt install -y build-essential cmake git g++',
        },
        {
            title: 'Install the arena CLI',
            description:
                'The installer detects the architecture and places the binary in ~/.local/bin.',
            command: 'curl -fsSL https://arena.dev/install.sh | sh',
        },
        {
            title: 'Authenticate the device',
            description:
                'Use the device code shown in the terminal. The token is stored with 0600 permissions.',
            command: 'arena auth login',
        },
        {
            title: 'Verify the installation',
            description:
                'Doctor reports the compiler versions, the sandbox connection, and any missing optional tools.',
            command: 'arena doctor',
        },
    ],
    windows: [
        {
            title: 'Install Git and CMake',
            description:
                'Run this in PowerShell. If winget is unavailable, install Git and CMake from their official installers.',
            command:
                'winget install --id Git.Git -e; winget install --id Kitware.CMake -e',
        },
        {
            title: 'Install LLVM and a C++ toolchain',
            description:
                'The CLI uses clang-cl from LLVM by default. Visual Studio Build Tools also work.',
            command: 'winget install --id LLVM.LLVM -e',
        },
        {
            title: 'Install the arena CLI',
            description:
                'The PowerShell installer adds arena to the current user PATH. Restart the terminal afterwards.',
            command: 'powershell -c "irm https://arena.dev/install.ps1 | iex"',
        },
        {
            title: 'Authenticate the device',
            description:
                'The browser opens for sign-in. On a headless machine, pass --device-code instead.',
            command: 'arena auth login',
        },
        {
            title: 'Verify the installation',
            description:
                'Doctor checks the toolchain and the sandbox connection. WSL is optional but recommended for perf tooling.',
            command: 'arena doctor',
        },
    ],
};

const doctorChecks: Array<{
    name: string;
    detail: string;
    status: CheckStatus;
}> = [
    {
        name: 'Compiler',
        detail: 'g++ 14.2.0 · clang 18.1.3',
        status: 'pass',
    },
    {
        name: 'CMake',
        detail: '3.30.2',
        status: 'pass',
    },
    {
        name: 'Git',
        detail: '2.46.0',
        status: 'pass',
    },
    {
        name: 'Arena API',
        detail: 'api.arena.dev · 42 ms round trip',
        status: 'pass',
    },
    {
        name: 'Sandbox',
        detail: 'isolated runner reachable',
        status: 'pass',
    },
    {
        name: 'Optional: TSan runtime',
        detail: 'libtsan not found · install libtsan0 for thread checks',
        status: 'warn',
    },
];

const initialSessions: Session[] = [
    {
        id: 'session-1',
        device: 'MacBook Pro 14',
        os: 'macOS 15.1',
        location: 'This device',
        lastUsed: 'Active now',
        token: 'arena_live_••••••••a91f',
        current: true,
    },
    {
        id: 'session-2',
        device: 'ThinkPad X1 Carbon',
        os: 'Ubuntu 24.04',
        location: 'Berlin, DE',
        lastUsed: '2 days ago',
        token: 'arena_live_••••••••77c2',
        current: false,
    },
    {
        id: 'session-3',
        device: 'CI runner',
        os: 'Ubuntu 22.04',
        location: 'GitHub Actions',
        lastUsed: '1 week ago',
        token: 'arena_live_••••••••31de',
        current: false,
    },
];

const troubleshooting = [
    {
        id: 'command-not-found',
        problem: 'arena: command not found',
        solution:
            'Add ~/.local/bin (or %USERPROFILE%\\.arena\\bin on Windows) to PATH, then open a new terminal. Run echo $PATH to confirm the directory is present.',
    },
    {
        id: 'auth-loop',
        problem:
            'The browser finishes sign-in but the CLI still says unauthenticated',
        solution:
            'The token is stored per user. If you ran the installer with sudo, the token was saved for root. Re-run arena auth login without sudo and check arena whoami.',
    },
    {
        id: 'compiler-missing',
        problem: 'Doctor reports that no supported compiler was found',
        solution:
            'Install GCC 13+ or clang 16+. On macOS, install Homebrew GCC because the system clang is older than the required standard library. Then run arena doctor --reset.',
    },
    {
        id: 'sandbox-timeout',
        problem: 'The sandbox connection times out',
        solution:
            'Corporate proxies frequently block the runner host. Set HTTPS_PROXY, then run arena doctor --network. If the proxy requires a certificate, set ARENA_CA_BUNDLE to its path.',
    },
    {
        id: 'tsan-missing',
        problem: 'ThreadSanitizer checks fail with a missing runtime',
        solution:
            'Install libtsan0 on Debian or Ubuntu, or compiler-rt on Fedora. TSan is optional; the CLI will skip the check until the runtime is available.',
    },
];

function displayUsername(value: string): string {
    const cleaned = value.replace(/\s+/g, '');
    return (cleaned === '' ? 'you' : cleaned).slice(0, 14);
}

export default function SetupIndex() {
    const { auth } = usePage().props;
    const username = displayUsername(auth.user.username ?? auth.user.name);

    const [os, setOs] = useState<OperatingSystem>('macos');
    const [doctorState, setDoctorState] = useState<DoctorState>('ready');
    const [lastRun, setLastRun] = useState('Today at 09:41');
    const [sessions, setSessions] = useState<Session[]>(initialSessions);
    const [token, setToken] = useState('');
    const [connectError, setConnectError] = useState(false);
    const [connecting, setConnecting] = useState(false);
    const [query, setQuery] = useState('');

    const steps = installSteps[os];

    const filteredTroubleshooting = useMemo(() => {
        const needle = query.trim().toLowerCase();

        if (needle === '') {
            return troubleshooting;
        }

        return troubleshooting.filter(
            (item) =>
                item.problem.toLowerCase().includes(needle) ||
                item.solution.toLowerCase().includes(needle),
        );
    }, [query]);

    const runDoctor = () => {
        setDoctorState('running');
        window.setTimeout(() => {
            setDoctorState('ready');
            setLastRun('Just now');
            toast.success('Doctor finished', {
                description: '6 checks run · 5 passed · 1 optional warning.',
            });
        }, 900);
    };

    const connectDevice = () => {
        if (
            !token.trim().startsWith('arena_live_') ||
            token.trim().length < 16
        ) {
            setConnectError(true);
            return;
        }

        setConnecting(true);
        window.setTimeout(() => {
            setConnecting(false);
            setConnectError(false);
            setToken('');
            setSessions((current) => [
                ...current,
                {
                    id: `session-${Date.now()}`,
                    device: 'New CLI device',
                    os: 'Detected on first run',
                    location: 'Awaiting first command',
                    lastUsed: 'Just now',
                    token: `${token.trim().slice(0, 12)}••••${token.trim().slice(-4)}`,
                    current: false,
                },
            ]);
            toast.success('Device connected', {
                description: 'The token is now active for this account.',
            });
        }, 800);
    };

    const revoke = (id: string) => {
        setSessions((current) =>
            current.filter((session) => session.id !== id),
        );
        toast.success('Session revoked', {
            description: 'The token can no longer be used.',
        });
    };

    return (
        <>
            <Head title="CLI setup" />

            <div className="mx-auto flex w-full max-w-7xl flex-1 flex-col gap-8 p-4 md:p-8">
                <PageHeader
                    eyebrow="Toolchain / device auth"
                    title="CLI setup"
                    description="Install the arena CLI, connect local toolchains, and manage device sessions. Tokens and authenticated devices stay in sync with this account."
                    meta={
                        <>
                            <span className="inline-flex items-center gap-1.5 font-mono text-[11px] text-muted-foreground">
                                <span className="size-1.5 rounded-full bg-emerald-500" />
                                Connected as {username}
                            </span>
                            <span className="font-mono text-[11px] text-muted-foreground">
                                arena 2.9.1
                            </span>
                        </>
                    }
                    actions={
                        <>
                            <Button
                                variant="outline"
                                size="sm"
                                asChild
                                className="gap-1.5 bg-transparent"
                            >
                                <Link href={securityRoute()}>
                                    <KeyRound className="size-3.5" />
                                    Devices & tokens
                                </Link>
                            </Button>
                            <Button
                                size="sm"
                                onClick={runDoctor}
                                className="gap-1.5"
                            >
                                <RefreshCw className="size-3.5" />
                                Run doctor
                            </Button>
                        </>
                    }
                />

                <div className="grid grid-cols-2 gap-x-6 gap-y-5 border-y border-border/80 py-5 sm:grid-cols-4">
                    <StatTile
                        label="Connection"
                        value="Connected"
                        hint={`Signed in as ${username}`}
                        icon={PlugZap}
                        tone="emerald"
                    />
                    <StatTile
                        label="CLI version"
                        value="2.9.1"
                        hint="Up to date"
                        icon={Command}
                    />
                    <StatTile
                        label="Toolchains"
                        value="2"
                        hint="GCC 14 · Clang 18"
                        icon={Cpu}
                    />
                    <StatTile
                        label="Sessions"
                        value={String(sessions.length)}
                        hint="Active device tokens"
                        icon={Laptop}
                    />
                </div>

                <div className="grid gap-5 xl:grid-cols-[minmax(0,1fr)_21rem]">
                    <div className="min-w-0 space-y-5">
                        <SectionCard
                            title="Install the CLI"
                            description="Pick your operating system. Every command is safe to copy, and the installer never needs sudo for the CLI itself."
                            icon={Download}
                        >
                            <SegmentedTabs<OperatingSystem>
                                ariaLabel="Operating system"
                                value={os}
                                onChange={setOs}
                                items={operatingSystems.map((item) => ({
                                    value: item.value,
                                    label: item.label,
                                    icon: item.icon,
                                }))}
                            />

                            <ol className="mt-5 space-y-4">
                                {steps.map((step, index) => (
                                    <li key={step.title} className="flex gap-3">
                                        <span className="flex size-6 shrink-0 items-center justify-center rounded-sm border border-foreground/20 bg-background font-mono text-[10px] font-semibold text-foreground">
                                            {String(index + 1).padStart(2, '0')}
                                        </span>
                                        <div className="min-w-0 flex-1 space-y-2">
                                            <div>
                                                <h3 className="text-sm font-semibold">
                                                    {step.title}
                                                </h3>
                                                <p className="mt-0.5 text-xs leading-5 text-muted-foreground">
                                                    {step.description}
                                                </p>
                                            </div>
                                            <CodeBlock value={step.command} />
                                        </div>
                                    </li>
                                ))}
                            </ol>

                            <Callout
                                variant="tip"
                                title="Then create a workspace"
                                className="mt-5"
                            >
                                Run{' '}
                                <code className="rounded bg-muted px-1 py-0.5 font-mono text-[11px]">
                                    arena new hello --lang cpp
                                </code>{' '}
                                and{' '}
                                <code className="rounded bg-muted px-1 py-0.5 font-mono text-[11px]">
                                    arena run
                                </code>{' '}
                                to confirm that a real build reaches the
                                sandbox.
                            </Callout>
                        </SectionCard>

                        <SectionCard
                            title="Doctor results"
                            description="A read-only snapshot of the local toolchain and the connection to the judge."
                            icon={ShieldCheck}
                            actions={
                                <>
                                    <span className="font-mono text-[10px] text-muted-foreground">
                                        Last run {lastRun}
                                    </span>
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        onClick={runDoctor}
                                        disabled={doctorState === 'running'}
                                    >
                                        <RefreshCw
                                            className={cn(
                                                'size-4',
                                                doctorState === 'running' &&
                                                    'animate-spin',
                                            )}
                                        />
                                        Re-run
                                    </Button>
                                </>
                            }
                        >
                            {doctorState === 'running' ? (
                                <LoadingState rows={5} />
                            ) : (
                                <>
                                    <div className="divide-y divide-border/70 rounded-md border border-border/90">
                                        {doctorChecks.map((check) => (
                                            <div
                                                key={check.name}
                                                className="flex flex-wrap items-center gap-3 px-3 py-2.5"
                                            >
                                                {check.status === 'pass' ? (
                                                    <CheckCircle2 className="size-4 shrink-0 text-emerald-600 dark:text-emerald-400" />
                                                ) : check.status === 'warn' ? (
                                                    <AlertTriangle className="size-4 shrink-0 text-amber-600" />
                                                ) : (
                                                    <XCircle className="size-4 shrink-0 text-rose-600" />
                                                )}
                                                <span className="w-40 shrink-0 text-xs font-medium">
                                                    {check.name}
                                                </span>
                                                <span className="min-w-0 flex-1 font-mono text-[11px] break-words text-muted-foreground">
                                                    {check.detail}
                                                </span>
                                                <span
                                                    className={cn(
                                                        'shrink-0 font-mono text-[10px] tracking-wider uppercase',
                                                        check.status ===
                                                            'pass' &&
                                                            'text-emerald-700 dark:text-emerald-400',
                                                        check.status ===
                                                            'warn' &&
                                                            'text-amber-700 dark:text-amber-400',
                                                        check.status ===
                                                            'fail' &&
                                                            'text-rose-700 dark:text-rose-400',
                                                    )}
                                                >
                                                    {check.status === 'pass'
                                                        ? 'Passed'
                                                        : check.status ===
                                                            'warn'
                                                          ? 'Warning'
                                                          : 'Failed'}
                                                </span>
                                            </div>
                                        ))}
                                    </div>

                                    <p className="mt-3 text-[11px] leading-5 text-muted-foreground">
                                        Warnings are optional. Submissions still
                                        run; only the related check is skipped.
                                        Failures block submission until the
                                        problem is fixed.
                                    </p>
                                </>
                            )}
                        </SectionCard>

                        <SectionCard
                            title="Troubleshooting"
                            description="The problems that account for almost every setup support request."
                            icon={CircleHelp}
                        >
                            <div className="relative mb-4 w-full sm:max-w-sm">
                                <Search className="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />
                                <Input
                                    value={query}
                                    onChange={(event) =>
                                        setQuery(event.target.value)
                                    }
                                    placeholder="Search the checklist..."
                                    className="pl-9"
                                    aria-label="Search troubleshooting checklist"
                                />
                            </div>

                            {filteredTroubleshooting.length === 0 ? (
                                <EmptyState
                                    icon={Search}
                                    title="No matching fixes"
                                    description="Try a shorter search term, or open the help centre and include the output of arena doctor in your report."
                                    action={
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            onClick={() => setQuery('')}
                                        >
                                            Clear search
                                        </Button>
                                    }
                                />
                            ) : (
                                <div className="space-y-2">
                                    {filteredTroubleshooting.map((item) => (
                                        <details
                                            key={item.id}
                                            className="group rounded-md border border-border/90 bg-card px-4 py-3"
                                        >
                                            <summary className="flex cursor-pointer list-none items-center gap-2 text-sm font-medium">
                                                <ChevronRight className="size-3.5 shrink-0 text-muted-foreground transition-transform group-open:rotate-90" />
                                                {item.problem}
                                            </summary>
                                            <p className="mt-2 pl-5 text-xs leading-5 text-muted-foreground">
                                                {item.solution}
                                            </p>
                                        </details>
                                    ))}
                                </div>
                            )}
                        </SectionCard>
                    </div>

                    <aside className="space-y-4">
                        <SectionCard
                            title="This device"
                            description="The session the CLI is currently using."
                            icon={Laptop}
                        >
                            <div className="space-y-3">
                                <div className="flex items-center gap-2">
                                    <span className="size-1.5 rounded-full bg-emerald-500" />
                                    <span className="font-mono text-[10px] tracking-wider text-emerald-700 uppercase dark:text-emerald-400">
                                        Connected
                                    </span>
                                </div>

                                <dl className="space-y-2 text-xs">
                                    <div className="flex justify-between gap-3">
                                        <dt className="text-muted-foreground">
                                            Account
                                        </dt>
                                        <dd className="font-mono font-medium">
                                            {username}
                                        </dd>
                                    </div>
                                    <div className="flex justify-between gap-3">
                                        <dt className="text-muted-foreground">
                                            Workspace
                                        </dt>
                                        <dd className="max-w-[9rem] truncate text-right font-mono text-[11px] font-medium">
                                            ~/arena/workspace
                                        </dd>
                                    </div>
                                    <div className="flex justify-between gap-3">
                                        <dt className="text-muted-foreground">
                                            API region
                                        </dt>
                                        <dd className="font-mono font-medium">
                                            eu-central
                                        </dd>
                                    </div>
                                    <div className="flex justify-between gap-3">
                                        <dt className="text-muted-foreground">
                                            Shown as
                                        </dt>
                                        <dd className="font-mono font-medium">
                                            {username}
                                        </dd>
                                    </div>
                                </dl>

                                <Button
                                    variant="outline"
                                    size="sm"
                                    className="w-full"
                                    asChild
                                >
                                    <Link href={securityRoute()}>
                                        Manage passkeys
                                    </Link>
                                </Button>
                            </div>
                        </SectionCard>

                        <SectionCard
                            title="Connect another device"
                            description="Paste a token from Settings → Devices and tokens."
                            icon={KeyRound}
                        >
                            <form
                                onSubmit={(event) => {
                                    event.preventDefault();
                                    connectDevice();
                                }}
                                className="space-y-2"
                            >
                                <label
                                    htmlFor="device-token"
                                    className="font-mono text-[10px] font-semibold tracking-widest text-muted-foreground uppercase"
                                >
                                    Device token
                                </label>
                                <Input
                                    id="device-token"
                                    value={token}
                                    onChange={(event) => {
                                        setToken(event.target.value);
                                        setConnectError(false);
                                    }}
                                    placeholder="arena_live_..."
                                    className="font-mono text-xs"
                                    autoComplete="off"
                                    aria-invalid={connectError}
                                />
                                <Button
                                    type="submit"
                                    size="sm"
                                    className="w-full"
                                    disabled={connecting}
                                >
                                    {connecting
                                        ? 'Connecting...'
                                        : 'Connect device'}
                                </Button>
                            </form>

                            {connectError && (
                                <div className="mt-3">
                                    <ErrorState
                                        title="That token is not valid"
                                        description="CLI tokens start with arena_live_ and are at least 16 characters. Create a new token under Settings → Devices and tokens."
                                        action={
                                            <Button
                                                variant="outline"
                                                size="sm"
                                                asChild
                                            >
                                                <Link href={securityRoute()}>
                                                    Open settings
                                                </Link>
                                            </Button>
                                        }
                                    />
                                </div>
                            )}

                            {!connectError && (
                                <p className="mt-3 text-[11px] leading-5 text-muted-foreground">
                                    Tokens are scoped to one account and can be
                                    revoked at any time. Never commit one to a
                                    repository.
                                </p>
                            )}
                        </SectionCard>

                        <div className="rounded-lg border border-amber-600/25 bg-amber-600/5 p-4">
                            <p className="meta-label flex items-center gap-1.5 text-amber-800 dark:text-amber-300">
                                <Users className="size-3.5" />
                                Team runners
                            </p>
                            <div className="mt-2 flex items-start gap-2">
                                <Lock className="mt-0.5 size-3.5 shrink-0 text-amber-700 dark:text-amber-400" />
                                <div>
                                    <p className="text-sm font-medium">
                                        Owner permission required
                                    </p>
                                    <p className="mt-1 text-[11px] leading-5 text-muted-foreground">
                                        Shared runner pools are managed by the
                                        workspace owner. You can use them but
                                        cannot change membership.
                                    </p>
                                </div>
                            </div>
                            <Button
                                variant="outline"
                                size="sm"
                                asChild
                                className="mt-3 w-full bg-transparent"
                            >
                                <Link href={securityRoute()}>View access</Link>
                            </Button>
                        </div>
                    </aside>
                </div>

                <SectionCard
                    title="Active CLI sessions"
                    description="Every token that can talk to the API on your behalf. Revoking a session takes effect immediately."
                    icon={Terminal}
                    actions={
                        <Button variant="outline" size="sm" asChild>
                            <Link href={securityRoute()}>
                                <KeyRound className="size-4" />
                                All devices and tokens
                            </Link>
                        </Button>
                    }
                >
                    {sessions.length === 0 ? (
                        <EmptyState
                            icon={KeyRound}
                            title="No active sessions"
                            description="Every device has been revoked. Run arena auth login on a machine to create a new session."
                        />
                    ) : (
                        <div className="overflow-hidden rounded-md border border-border/90">
                            <div className="hidden grid-cols-[minmax(0,1fr)_12rem_8rem_6rem] gap-3 border-b border-border/80 bg-muted/40 px-4 py-2 font-mono text-[10px] font-medium tracking-wider text-muted-foreground uppercase md:grid">
                                <span>Device</span>
                                <span>Token</span>
                                <span>Last used</span>
                                <span className="text-right">Action</span>
                            </div>
                            <div className="divide-y divide-border/70">
                                {sessions.map((session) => (
                                    <div
                                        key={session.id}
                                        className="grid gap-2 px-4 py-3 md:grid-cols-[minmax(0,1fr)_12rem_8rem_6rem] md:items-center md:gap-3"
                                    >
                                        <div className="min-w-0">
                                            <div className="flex flex-wrap items-center gap-2">
                                                <span className="text-sm font-medium">
                                                    {session.device}
                                                </span>
                                                {session.current && (
                                                    <Pill tone="emerald">
                                                        This device
                                                    </Pill>
                                                )}
                                            </div>
                                            <p className="mt-0.5 text-[11px] text-muted-foreground">
                                                {session.os} ·{' '}
                                                {session.location}
                                            </p>
                                        </div>
                                        <span className="font-mono text-[11px] text-muted-foreground">
                                            {session.token}
                                        </span>
                                        <span className="text-[11px] text-muted-foreground">
                                            {session.lastUsed}
                                        </span>
                                        <div className="flex md:justify-end">
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                className="h-7 px-2 text-xs text-muted-foreground hover:text-rose-600"
                                                onClick={() =>
                                                    revoke(session.id)
                                                }
                                                disabled={session.current}
                                                aria-label={`Revoke ${session.device}`}
                                            >
                                                <Trash2 className="size-3.5" />
                                                Revoke
                                            </Button>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    )}
                </SectionCard>
            </div>
        </>
    );
}

SetupIndex.layout = {
    breadcrumbs: [
        {
            title: 'Setup',
            href: '/setup',
        },
    ],
};
