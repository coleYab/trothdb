import { Head, Link, usePage } from '@inertiajs/react';
import { BookOpen, Cpu, Layers, Menu, X } from 'lucide-react';
import { useState } from 'react';
import { WunderFrame } from '@/components/wunder-frame';
import { dashboard, login } from '@/routes';
import { index as problemsetRoute } from '@/routes/problemset';

type ProblemDemo = {
    id: string;
    title: string;
    subtitle: string;
    description: string[];
};

const DEMO_PROBLEMS: ProblemDemo[] = [
    {
        id: 'call-stacks',
        title: 'Yeah, I know what a call stack is.',
        subtitle:
            'Reconstructing function call timings from sampling profiler data',
        description: [
            "This medium-difficulty coding problem tests your ability to reconstruct precise event timings from incomplete, periodic snapshots of a running program's call stack. It appears frequently in performance-profiling roles at firms that build trading infrastructure, monitoring systems, or developer tools.",
            'The core challenge is to infer when functions began and ended execution by comparing consecutive stack snapshots taken at irregular intervals. You must track which functions are entering (appearing for the first time in a stack) and exiting (disappearing from the stack), emit events with correct timestamps, and handle edge cases like empty inputs and programs that start or stop mid-execution. The logic hinges on careful stack comparison: diffing the previous and current stacks to determine which functions are new entries and which have returned.',
        ],
    },
    {
        id: 'std-any',
        title: 'std::any: Type Erasure & Small Object Optimization',
        subtitle:
            'Zero dynamic allocations for inline buffers with custom vtable dispatch',
        description: [
            'In latency-critical trading systems and kernel bypass gateways, runtime polymorphism and dynamic heap allocation in the hot path introduce fatal cache misses and latency jitter. In this problem, you implement a type-erased container modeled on C++17 std::any, engineered with Small Object Optimization (SOO).',
            'Your container must hold instances of any copy-constructible and movable type. For objects below 32 bytes and matching alignment, construct them in-place within a stack buffer using placement new with zero heap allocation. Larger objects must be allocated on the heap. You must build custom vtable dispatch tables for copying, moving, and destroying values, and implement a type-safe any_cast<T>.',
        ],
    },
    {
        id: 'lru-cache',
        title: 'LRU Cache: High-Throughput O(1) Memory Engine',
        subtitle:
            'Constant-time eviction and lookups under heavy concurrent read/write load',
        description: [
            'Order book gateways and matching engines demand deterministic caching layers that evict stale market depth with zero latency jitter. Design an in-memory storage engine that operates as a fixed-capacity Least Recently Used (LRU) cache supporting high-throughput key-value operations.',
            'Both get(key) and put(key, value) must strictly run in O(1) average time. Accessing a key refreshes its recency to the head of the chain. When capacity is exceeded, atomically evict the least recently accessed key without scanning the list. Your engine must avoid memory fragmentation and handle in-place updates without double-counting capacity.',
        ],
    },
];

export default function Welcome() {
    const { auth } = usePage().props;
    const [selectedProblem, setSelectedProblem] = useState<ProblemDemo>(
        DEMO_PROBLEMS[0],
    );
    const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);

    return (
        <>
            <Head title="learnCP — The Competitive Programming Arena" />

            {/* WunderFrame: comfortable darker background, zero black border, fixed video background, no scrollbar */}
            <WunderFrame contentClassName="flex flex-col pb-40 pt-24 sm:pt-28">
                {/* Fixed Top Layout: Logo on Left, Only 4 Navigation Buttons on Right */}
                <header className="pointer-events-none fixed top-4 right-4 left-4 z-40 flex items-start justify-between sm:top-6 sm:right-8 sm:left-8">
                    {/* Fixed Logo in Redaction 50 Font */}
                    <Link
                        href="/"
                        className="pointer-events-auto font-['Redaction_50',serif] text-2xl font-normal tracking-tight text-[#FFF9D9] transition-opacity hover:opacity-90 sm:text-3xl"
                    >
                        learn<span className="text-emerald-400">CP</span>
                    </Link>

                    {/* Desktop Right Menu: Enlarged 4 Buttons (problemset, dashboard, leaderboard, docs) */}
                    <div className="pointer-events-auto hidden flex-col items-end gap-2 font-mono text-xs md:flex">
                        <Link
                            href={problemsetRoute()}
                            className="w-fit rounded-xl border border-white/20 bg-black/60 px-5 py-2 text-xs font-semibold text-[#FFF9D9] shadow-md backdrop-blur-md transition-all hover:scale-105 hover:bg-black/85 sm:text-sm"
                        >
                            problemset
                        </Link>
                        <Link
                            href={auth.user ? dashboard() : login()}
                            className="w-fit rounded-xl border border-white/20 bg-black/60 px-5 py-2 text-xs font-semibold text-[#FFF9D9] shadow-md backdrop-blur-md transition-all hover:scale-105 hover:bg-black/85 sm:text-sm"
                        >
                            dashboard
                        </Link>
                        <Link
                            href={problemsetRoute()}
                            className="w-fit rounded-xl border border-white/20 bg-black/60 px-5 py-2 text-xs font-semibold text-[#FFF9D9] shadow-md backdrop-blur-md transition-all hover:scale-105 hover:bg-black/85 sm:text-sm"
                        >
                            leaderboard
                        </Link>
                        <a
                            href="#pillars"
                            className="w-fit rounded-xl border border-white/20 bg-black/60 px-5 py-2 text-xs font-semibold text-[#FFF9D9] shadow-md backdrop-blur-md transition-all hover:scale-105 hover:bg-black/85 sm:text-sm"
                        >
                            docs
                        </a>
                    </div>

                    {/* Mobile Experience: Enlarged Collapsible Glass Menu with the exact same 4 buttons */}
                    <div className="pointer-events-auto relative flex flex-col items-end md:hidden">
                        <button
                            type="button"
                            onClick={() => setIsMobileMenuOpen((prev) => !prev)}
                            className="inline-flex items-center gap-2 rounded-xl border border-white/25 bg-black/65 px-4 py-2.5 font-mono text-xs font-semibold text-[#FFF9D9] shadow-xl backdrop-blur-md transition-all hover:bg-black/85 sm:text-sm"
                            aria-expanded={isMobileMenuOpen}
                            aria-label="Toggle navigation menu"
                        >
                            {isMobileMenuOpen ? (
                                <X className="size-4 text-[#FFF9D9]" />
                            ) : (
                                <Menu className="size-4 text-[#FFF9D9]" />
                            )}
                            <span>{isMobileMenuOpen ? 'close' : 'menu'}</span>
                        </button>

                        {/* Collapsible Dropdown for Mobile (Enlarged 4 buttons) */}
                        {isMobileMenuOpen && (
                            <div className="mt-2.5 flex animate-in flex-col items-end gap-2 font-mono text-xs duration-200 fade-in slide-in-from-top-2 sm:text-sm">
                                <Link
                                    href={problemsetRoute()}
                                    onClick={() => setIsMobileMenuOpen(false)}
                                    className="w-fit rounded-xl border border-white/20 bg-black/85 px-5 py-2.5 font-semibold text-[#FFF9D9] shadow-lg backdrop-blur-xl"
                                >
                                    problemset
                                </Link>
                                <Link
                                    href={auth.user ? dashboard() : login()}
                                    onClick={() => setIsMobileMenuOpen(false)}
                                    className="w-fit rounded-xl border border-white/20 bg-black/85 px-5 py-2.5 font-semibold text-[#FFF9D9] shadow-lg backdrop-blur-xl"
                                >
                                    dashboard
                                </Link>
                                <Link
                                    href={problemsetRoute()}
                                    onClick={() => setIsMobileMenuOpen(false)}
                                    className="w-fit rounded-xl border border-white/20 bg-black/85 px-5 py-2.5 font-semibold text-[#FFF9D9] shadow-lg backdrop-blur-xl"
                                >
                                    leaderboard
                                </Link>
                                <a
                                    href="#pillars"
                                    onClick={() => setIsMobileMenuOpen(false)}
                                    className="w-fit rounded-xl border border-white/20 bg-black/85 px-5 py-2.5 font-semibold text-[#FFF9D9] shadow-lg backdrop-blur-xl"
                                >
                                    docs
                                </a>
                            </div>
                        )}
                    </div>
                </header>

                {/* Hero View: Centered, Compact Whitespace, Redaction 50 Title */}
                <section className="relative flex min-h-[calc(100vh-14rem)] w-full flex-col items-center justify-center px-4 py-6 text-center sm:px-6">
                    <div className="mx-auto flex w-full max-w-4xl flex-col items-center justify-center">
                        {/* Redaction 50 Hero Title */}
                        <h1 className="font-['Redaction_50',serif] text-4xl leading-[1.08] font-normal tracking-tight text-[#FFF9D9] [text-shadow:0_0_20px_rgba(255,249,217,0.4)] sm:text-6xl md:text-7xl">
                            Competitive Programming
                            <br />
                            Algorithm Arena
                        </h1>

                        {/* Centered Glassmorphic Statement Card */}
                        <div className="mt-5 w-full max-w-xl rounded-2xl border border-white/15 bg-black/40 px-6 py-4.5 text-center shadow-xl backdrop-blur-md md:px-8 md:py-5">
                            <p className="font-mono text-xs leading-relaxed font-semibold tracking-wider text-[#FFF9D9] uppercase md:text-sm">
                                Predict complexity from constraints.
                                <br />
                                Write fast, bug-free code.
                                <br />
                                Solve the problems quants &amp; FAANG face
                                daily.
                            </p>
                        </div>
                    </div>
                </section>

                {/* The Problem Showcase: Code, compile & submit from your local environment */}
                <section
                    id="arena"
                    className="mx-auto w-full max-w-4xl px-4 py-8 sm:px-6"
                >
                    <div className="text-center">
                        <h2 className="font-['Redaction_50',serif] text-3xl font-normal tracking-tight text-[#FFF9D9] [text-shadow:0_0_12px_rgba(255,249,217,0.25)] sm:text-4xl">
                            Code, compile &amp; submit from your local
                            environment.
                        </h2>
                        <p className="mx-auto mt-1 max-w-xl font-mono text-xs text-[#FFF9D9]/75 sm:text-sm">
                            Solve challenging systems and quant engineering
                            problems with your own local tools, IDEs, and
                            compilers.
                        </p>
                    </div>

                    {/* Glassmorphic Problem Card: Fixed height + folded description + tag-free light buttons */}
                    <div className="mt-6 flex h-[31rem] flex-col overflow-hidden rounded-2xl border border-white/15 bg-black/55 shadow-2xl ring-1 ring-white/10 backdrop-blur-2xl sm:h-[29rem]">
                        {/* Tabs Bar: Lighter, larger buttons with tags hidden and horizontal scroll */}
                        <div className="flex shrink-0 items-center border-b border-white/10 bg-black/60 px-4 py-3 sm:px-6">
                            <div className="flex w-full [scrollbar-width:none] items-center gap-2.5 overflow-x-auto [-ms-overflow-style:none] sm:gap-3 [&::-webkit-scrollbar]:hidden">
                                {DEMO_PROBLEMS.map((prob) => {
                                    const isSelected =
                                        selectedProblem.id === prob.id;
                                    return (
                                        <button
                                            key={prob.id}
                                            type="button"
                                            onClick={() =>
                                                setSelectedProblem(prob)
                                            }
                                            className={`shrink-0 rounded-xl px-5 py-2.5 font-mono text-xs font-bold transition-all sm:px-6 sm:py-2.5 sm:text-sm ${
                                                isSelected
                                                    ? 'border border-white/40 bg-[#FFF9D9] text-slate-950 shadow-md hover:bg-white'
                                                    : 'border border-white/20 bg-white/10 text-[#FFF9D9] hover:border-white/30 hover:bg-white/20 hover:text-white'
                                            }`}
                                        >
                                            {prob.id === 'call-stacks'
                                                ? 'Call Stacks'
                                                : prob.id === 'std-any'
                                                  ? 'std::any'
                                                  : 'LRU Cache'}
                                        </button>
                                    );
                                })}
                            </div>
                        </div>

                        {/* Problem Details Body: Clean prose, zero tag clutter, and centered Read More button */}
                        <div className="flex flex-1 [scrollbar-width:none] flex-col justify-between overflow-y-auto p-6 [-ms-overflow-style:none] md:p-8 [&::-webkit-scrollbar]:hidden">
                            <div>
                                {/* Title & Subtitle Header (Tags removed) */}
                                <div>
                                    <h3 className="font-['Redaction_50',serif] text-2xl font-normal text-[#FFF9D9] sm:text-3xl">
                                        {selectedProblem.title}
                                    </h3>
                                    <p className="mt-1 font-mono text-xs font-semibold text-emerald-400 sm:text-sm">
                                        {selectedProblem.subtitle}
                                    </p>
                                </div>

                                {/* Problem Prose Description: Folded after 3 lines of paragraph 2 for perfect framing */}
                                <div className="mt-5 space-y-3 font-mono text-xs leading-relaxed text-[#FFF9D9]/90 sm:text-sm">
                                    <p>{selectedProblem.description[0]}</p>
                                    {selectedProblem.description[1] && (
                                        <p className="line-clamp-3 text-[#FFF9D9]/75">
                                            {selectedProblem.description[1]}
                                        </p>
                                    )}
                                </div>
                            </div>

                            {/* Centered Read More Button */}
                            <div className="mt-6 flex shrink-0 justify-center">
                                <Link
                                    href={problemsetRoute()}
                                    className="inline-flex items-center justify-center gap-2 rounded-2xl bg-[#FFF9D9] px-10 py-3 font-mono text-xs font-bold tracking-wider text-slate-950 uppercase shadow-xl transition-all hover:scale-105 hover:bg-white active:scale-95"
                                >
                                    <span>Read More →</span>
                                </Link>
                            </div>
                        </div>
                    </div>
                </section>

                {/* Architecture Pillars: 1 Card Per Row */}
                <section
                    id="pillars"
                    className="mx-auto w-full max-w-4xl px-4 py-8 sm:px-6"
                >
                    <div className="text-center">
                        <h2 className="font-['Redaction_50',serif] text-3xl font-normal tracking-tight text-[#FFF9D9] [text-shadow:0_0_12px_rgba(255,249,217,0.25)] sm:text-4xl">
                            Mirror your practice with the actual arena.
                        </h2>
                    </div>

                    {/* 1 Card per row (Cards 1, 2, 3 only) */}
                    <div className="mt-6 flex flex-col gap-3.5">
                        <PillarCard
                            icon={Layers}
                            title="1. Curated Problem Tracks"
                            description="Zero puzzle trivia. Advance logically from call-stack profiling and memory allocators to interval trees, cache eviction engines, and lock-free data structures."
                        />
                        <PillarCard
                            icon={Cpu}
                            title="2. Sub-Millisecond Judge"
                            description="Stress-tested against tight memory limits and hidden edge cases that catch quadratic timeouts, duplicate keys, and overflows."
                        />
                        <PillarCard
                            icon={BookOpen}
                            title="3. Docs-Grade Editorials"
                            description="Step-by-step mathematical reasoning, proofs of correctness, and time/space complexity analysis inspired by the Laravel docs."
                        />
                    </div>
                </section>

                {/* Fixed & Centered "enter the arena →" Button: Increased Size and Width */}
                <div className="pointer-events-none fixed bottom-4 left-1/2 z-40 w-full max-w-lg -translate-x-1/2 px-4 sm:bottom-6">
                    <Link
                        href={problemsetRoute()}
                        className="pointer-events-auto flex w-full items-center justify-center rounded-2xl bg-[#FFF9D9] py-4.5 font-mono text-base font-bold tracking-wide text-slate-950 shadow-[0_8px_40px_rgba(0,0,0,0.7)] transition-all hover:scale-[1.02] hover:bg-white active:scale-95 sm:py-5 sm:text-lg"
                    >
                        enter the arena →
                    </Link>
                </div>
            </WunderFrame>
        </>
    );
}

function PillarCard({
    icon: Icon,
    title,
    description,
}: {
    icon: React.ElementType;
    title: string;
    description: string;
}) {
    return (
        <div className="group flex w-full flex-col gap-3 rounded-2xl border border-white/15 bg-black/45 p-5 shadow-xl backdrop-blur-xl transition-all hover:border-white/30 hover:bg-black/55 sm:flex-row sm:items-center sm:gap-5 sm:p-6">
            <div className="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-emerald-400/30 bg-emerald-500/15 text-emerald-300 shadow-[0_0_15px_rgba(16,185,129,0.2)] transition-transform group-hover:scale-105">
                <Icon className="size-5" />
            </div>
            <div className="min-w-0 flex-1">
                <h3 className="font-['Redaction_50',serif] text-xl font-normal text-[#FFF9D9]">
                    {title}
                </h3>
                <p className="mt-1 font-mono text-xs leading-relaxed text-[#FFF9D9]/75 sm:text-sm">
                    {description}
                </p>
            </div>
        </div>
    );
}
