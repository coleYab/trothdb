import { Head, Link, router, setLayoutProps } from '@inertiajs/react';
import {
    AlertCircle,
    ArrowLeft,
    BookOpen,
    Check,
    CheckCircle2,
    Code2,
    Cpu,
    FileCode,
    FlaskConical,
    Layers,
    Lightbulb,
    ListChecks,
    Lock,
    Plus,
    RotateCcw,
    Send,
    Shield,
    Sparkles,
    Terminal,
    Trash2,
    Unlock,
} from 'lucide-react';
import { useEffect, useMemo, useState } from 'react';
import { toast } from 'sonner';
import { PageHeader } from '@/components/hub-ui';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { cn } from '@/lib/utils';
import { index as problemsetRoute } from '@/routes/problemset';
import type { Difficulty } from '@/types';

type TestGroup = 'basic' | 'edge' | 'cycle' | 'scale' | 'perf';

type CppTestCase = {
    case_id: string;
    name: string;
    group: TestGroup;
    points: number;
    time_limit_ms: number;
    hidden: boolean;
    test_code_cpp: string;
};

const testGroups: TestGroup[] = ['basic', 'edge', 'cycle', 'scale', 'perf'];

const groupColors: Record<TestGroup, string> = {
    basic: 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-500/30',
    edge: 'bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-500/30',
    cycle: 'bg-violet-500/10 text-violet-700 dark:text-violet-400 border-violet-500/30',
    scale: 'bg-sky-500/10 text-sky-700 dark:text-sky-400 border-sky-500/30',
    perf: 'bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-500/30',
};

const DEFAULT_STARTER_CPP = `#include <vector>
#include <string>

class Solution {
public:
    std::vector<std::string> generate(int n) {
        // TODO: Implement C++20 solution
        return {};
    }
};`;

const DEFAULT_JUDGE_SOLUTION = `#pragma once
#include <vector>
#include <string>

namespace judge {
class Solution {
public:
    std::vector<std::string> generate(int n) {
        std::vector<std::string> result;
        result.reserve(n);
        for (int i = 1; i <= n; ++i) {
            if (i % 15 == 0) result.push_back("FizzBuzz");
            else if (i % 3 == 0) result.push_back("Fizz");
            else if (i % 5 == 0) result.push_back("Buzz");
            else result.push_back(std::to_string(i));
        }
        return result;
    }
};
}`;

const DEFAULT_JUDGE_TEST_H = `#pragma once
#include <iostream>
#include <vector>
#include <string>
#include <cassert>

#define ASSERT_EQ(a, b, msg) \\
    if ((a) != (b)) { \\
        std::cerr << "Assertion Failed [" << (msg) << "]: " << (a) << " != " << (b) << std::endl; \\
        std::exit(1); \\
    }

#define COMPARE_ARRAYS(actual, expected, msg) \\
    if ((actual).size() != (expected).size()) { \\
        std::cerr << "Size mismatch [" << (msg) << "]: expected " << (expected).size() << ", got " << (actual).size() << std::endl; \\
        std::exit(1); \\
    } \\
    for (size_t i = 0; i < (actual).size(); ++i) { \\
        if ((actual)[i] != (expected)[i]) { \\
            std::cerr << "Mismatch at index " << i << " [" << (msg) << "]" << std::endl; \\
            std::exit(1); \\
        } \\
    }`;

export default function ProblemCreate({
    nextNumber = 19,
}: {
    nextNumber?: number;
}) {
    // 1. Problem Specification Metadata
    const [title, setTitle] = useState('');
    const [id, setId] = useState('');
    const [isCustomSlug, setIsCustomSlug] = useState(false);
    const [difficulty, setDifficulty] = useState<Difficulty>('Easy');
    const [discipline, setDiscipline] = useState('Systems');
    const [topic, setTopic] = useState('Algorithms');
    const [tags, setTags] = useState('Array, String, C++20');
    const [summary, setSummary] = useState('');
    const [ioMode, setIoMode] = useState<'harness' | 'stdio'>('harness');
    const [timeLimitMs, setTimeLimitMs] = useState(2000);
    const [memoryLimitMb, setMemoryLimitMb] = useState(64);

    // 2. Task Description & Constraints
    const [description, setDescription] = useState('');
    const [constraints, setConstraints] = useState('0 <= n <= 100000\nZero dynamic heap allocation on hot path');
    const [followUp, setFollowUp] = useState('');
    const [hints, setHints] = useState('');

    // 3. C++ Assets
    const [starterCode, setStarterCode] = useState(DEFAULT_STARTER_CPP);
    const [judgeSolution, setJudgeSolution] = useState(DEFAULT_JUDGE_SOLUTION);
    const [judgeTestH, setJudgeTestH] = useState(DEFAULT_JUDGE_TEST_H);

    // 4. Individual C++ Assertion Test Cases (Specification 2.0)
    const [testCases, setTestCases] = useState<CppTestCase[]>([
        {
            case_id: 'p1_one',
            name: 'Single Element (n=1)',
            group: 'basic',
            points: 20,
            time_limit_ms: 2000,
            hidden: false,
            test_code_cpp: `#include "judge_test.h"
#define Solution UserSolution
#include "solution.cpp"
#undef Solution
#include "judge_solution.hpp"

int main() {
    judge::Solution j; UserSolution u;
    COMPARE_ARRAYS(u.generate(1), j.generate(1), "n=1");
    return 0;
}`,
        },
        {
            case_id: 'p2_multiples',
            name: 'Divisibility Multiples (n=15)',
            group: 'basic',
            points: 30,
            time_limit_ms: 2000,
            hidden: false,
            test_code_cpp: `#include "judge_test.h"
#define Solution UserSolution
#include "solution.cpp"
#undef Solution
#include "judge_solution.hpp"

int main() {
    judge::Solution j; UserSolution u;
    COMPARE_ARRAYS(u.generate(15), j.generate(15), "n=15");
    return 0;
}`,
        },
        {
            case_id: 'h1_zero_edge',
            name: 'Boundary Zero Input (n=0)',
            group: 'edge',
            points: 20,
            time_limit_ms: 2000,
            hidden: true,
            test_code_cpp: `#include "judge_test.h"
#define Solution UserSolution
#include "solution.cpp"
#undef Solution
#include "judge_solution.hpp"

int main() {
    judge::Solution j; UserSolution u;
    COMPARE_ARRAYS(u.generate(0), j.generate(0), "n=0");
    return 0;
}`,
        },
        {
            case_id: 'h2_scale_stress',
            name: 'High-Volume Scale Stress (n=100,000)',
            group: 'scale',
            points: 30,
            time_limit_ms: 2000,
            hidden: true,
            test_code_cpp: `#include "judge_test.h"
#define Solution UserSolution
#include "solution.cpp"
#undef Solution
#include "judge_solution.hpp"

int main() {
    judge::Solution j; UserSolution u;
    COMPARE_ARRAYS(u.generate(100000), j.generate(100000), "n=100000");
    return 0;
}`,
        },
    ]);

    const [isSubmitting, setIsSubmitting] = useState(false);

    useEffect(() => {
        setLayoutProps({
            breadcrumbs: [
                {
                    title: 'Problemset',
                    href: problemsetRoute(),
                },
                {
                    title: 'Create Problem (Spec 2.0)',
                    href: '/problems/create',
                },
            ],
        });
    }, []);

    // Auto-generate slug from title
    useEffect(() => {
        if (!isCustomSlug && title) {
            const slug = title
                .toLowerCase()
                .trim()
                .replace(/[^\w\s-]/g, '')
                .replace(/[\s_-]+/g, '-')
                .replace(/^-+|-+$/g, '');
            setId(slug);
        }
    }, [title, isCustomSlug]);

    // Sum of points across all individual test cases
    const totalPoints = useMemo(() => {
        return testCases.reduce((sum, tc) => sum + (Number(tc.points) || 0), 0);
    }, [testCases]);

    const publicCount = testCases.filter((tc) => !tc.hidden).length;
    const hiddenCount = testCases.filter((tc) => tc.hidden).length;
    const isBalanced = totalPoints === 100;

    const handleAutoBalance = () => {
        if (testCases.length === 0) return;
        const equalShare = Math.floor(100 / testCases.length);
        const remainder = 100 - equalShare * testCases.length;

        setTestCases((prev) =>
            prev.map((tc, idx) => ({
                ...tc,
                points: idx === 0 ? equalShare + remainder : equalShare,
            })),
        );
        toast.success(`Balanced points equally across ${testCases.length} tests (Total: 100 pts)`);
    };

    const addTestCase = (hidden = false) => {
        const prefix = hidden ? 'h' : 'p';
        const nextIdx = testCases.filter((tc) => tc.hidden === hidden).length + 1;
        const caseId = `${prefix}${nextIdx}_case`;

        setTestCases((prev) => [
            ...prev,
            {
                case_id: caseId,
                name: hidden ? `Hidden Test ${nextIdx}` : `Public Test ${nextIdx}`,
                group: hidden ? 'edge' : 'basic',
                points: 10,
                time_limit_ms: 2000,
                hidden,
                test_code_cpp: `#include "judge_test.h"
#define Solution UserSolution
#include "solution.cpp"
#undef Solution
#include "judge_solution.hpp"

int main() {
    judge::Solution j; UserSolution u;
    // Assertion test against judge reference
    return 0;
}`,
            },
        ]);
    };

    const removeTestCase = (index: number) => {
        if (testCases.length <= 1) {
            toast.error('At least one C++ test case is required.');
            return;
        }
        setTestCases((prev) => prev.filter((_, i) => i !== index));
    };

    const updateTestCase = (index: number, field: keyof CppTestCase, val: any) => {
        setTestCases((prev) =>
            prev.map((tc, i) => (i === index ? { ...tc, [field]: val } : tc)),
        );
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        if (!title.trim()) {
            toast.error('Problem title is required.');
            return;
        }
        if (!summary.trim()) {
            toast.error('Problem summary is required.');
            return;
        }
        if (!description.trim()) {
            toast.error('Description is required.');
            return;
        }
        if (!starterCode.trim()) {
            toast.error('Starter C++ code is required.');
            return;
        }

        const invalidTc = testCases.some((tc) => !tc.case_id.trim() || !tc.test_code_cpp.trim());
        if (invalidTc) {
            toast.error('All C++ test cases must have a Case ID and test code.');
            return;
        }

        setIsSubmitting(true);

        const payload = {
            title: title.trim(),
            id: id.trim() || undefined,
            difficulty,
            discipline,
            topic: topic.trim() || undefined,
            tags: tags.trim(),
            summary: summary.trim(),
            description: description.trim(),
            constraints: constraints.trim(),
            follow_up: followUp.trim() || undefined,
            starter_code: starterCode.trim(),
            io_mode: ioMode,
            total_points: totalPoints,
            time_limit_ms: timeLimitMs,
            memory_limit_mb: memoryLimitMb,
            judge_solution: judgeSolution.trim(),
            judge_test_h: judgeTestH.trim(),
            hints: hints.trim(),
            examples: [
                {
                    input: 'Standard test input',
                    output: 'Expected reference output',
                    explanation: 'Verified against comparative judge reference.',
                },
            ],
            test_cases: testCases.map((tc) => ({
                case_id: tc.case_id,
                name: tc.name,
                group: tc.group,
                grade: tc.points,
                time_limit_ms: tc.time_limit_ms,
                hidden: tc.hidden,
                test_code_cpp: tc.test_code_cpp,
                input: tc.name,
                expected: `C++ Assertion [${tc.group}]`,
            })),
        };

        router.post('/problems', payload, {
            onError: (errors) => {
                setIsSubmitting(false);
                const first = Object.values(errors)[0];
                toast.error(typeof first === 'string' ? first : 'Validation error.');
            },
            onFinish: () => {
                setIsSubmitting(false);
            },
        });
    };

    return (
        <>
            <Head title="Create C++ Problem (Spec 2.0)" />

            <div className="mx-auto flex w-full max-w-7xl flex-1 flex-col gap-6 p-4 md:p-8">
                <Link
                    href={problemsetRoute()}
                    className="inline-flex w-fit items-center gap-1.5 font-mono text-[11px] text-muted-foreground transition-colors hover:text-foreground"
                >
                    <ArrowLeft className="size-3.5" />
                    Problemset
                </Link>

                <PageHeader
                    eyebrow={`Spec 2.0 / Problem #${nextNumber}`}
                    title="Create a C++ problem"
                    description="Individual C++ assertion files, a comparative judge reference, and per-case grading. Every case ships as a standalone program that exits non-zero on failure."
                    meta={
                        <>
                            <span className="inline-flex items-center gap-1.5 font-mono text-[11px] text-muted-foreground">
                                <Code2 className="size-3.5" />
                                C++20 only
                            </span>
                            <span className="inline-flex items-center gap-1.5 font-mono text-[11px] text-muted-foreground">
                                <Shield className="size-3.5" />
                                Hidden cases never reach the browser
                            </span>
                        </>
                    }
                />

                <form
                    onSubmit={handleSubmit}
                    className="grid gap-8 xl:grid-cols-[minmax(0,1fr)_19rem] xl:items-start xl:gap-10"
                >
                    <div className="min-w-0 space-y-6">
                    {/* SECTION 1: Core Problem Metadata */}
                    <section className="space-y-4 rounded-lg border border-border/90 bg-card p-5">
                        <div className="flex items-center gap-2 border-b border-border/70 pb-3 text-sm font-semibold text-foreground">
                            <Layers className="size-4 text-muted-foreground" />
                            <span>1. Problem Specification Metadata</span>
                        </div>

                        <div className="grid gap-4 sm:grid-cols-2">
                            <div className="space-y-1.5 sm:col-span-2">
                                <Label htmlFor="title">Challenge Title *</Label>
                                <Input
                                    id="title"
                                    value={title}
                                    onChange={(e) => setTitle(e.target.value)}
                                    placeholder="e.g. FizzBuzz or SPSC Ring Buffer"
                                    required
                                />
                            </div>

                            <div className="space-y-1.5">
                                <Label htmlFor="slug">
                                    Slug / ID{' '}
                                    <span className="text-[11px] text-muted-foreground">
                                        (/problems/{id || '...'})
                                    </span>
                                </Label>
                                <Input
                                    id="slug"
                                    value={id}
                                    onChange={(e) => {
                                        setIsCustomSlug(true);
                                        setId(e.target.value);
                                    }}
                                    placeholder="e.g. fizzbuzz"
                                    required
                                />
                            </div>

                            <div className="space-y-1.5">
                                <Label>Discipline Track *</Label>
                                <Input
                                    value={discipline}
                                    onChange={(e) => setDiscipline(e.target.value)}
                                    placeholder="e.g. Systems, Concurrency, Algorithms"
                                    required
                                />
                            </div>

                            <div className="space-y-1.5">
                                <Label>Difficulty *</Label>
                                <div className="flex gap-2">
                                    {(['Easy', 'Medium', 'Hard'] as Difficulty[]).map((d) => (
                                        <button
                                            key={d}
                                            type="button"
                                            onClick={() => setDifficulty(d)}
                                            className={cn(
                                                'flex-1 rounded-sm border py-1.5 font-mono text-[11px] transition-colors',
                                                difficulty === d
                                                    ? d === 'Easy'
                                                        ? 'border-emerald-600/40 bg-emerald-600/10 text-emerald-800 dark:text-emerald-400'
                                                        : d === 'Medium'
                                                          ? 'border-amber-600/40 bg-amber-600/10 text-amber-800 dark:text-amber-400'
                                                          : 'border-rose-600/40 bg-rose-600/10 text-rose-800 dark:text-rose-400'
                                                    : 'border-border/80 bg-muted/20 text-muted-foreground hover:bg-muted',
                                            )}
                                        >
                                            {d}
                                        </button>
                                    ))}
                                </div>
                            </div>

                            <div className="space-y-1.5">
                                <Label htmlFor="topic">Topic</Label>
                                <Input
                                    id="topic"
                                    value={topic}
                                    onChange={(e) => setTopic(e.target.value)}
                                    placeholder="e.g. Memory Layout, Cache & SIMD, Lock-Free"
                                />
                            </div>

                            <div className="space-y-1.5 sm:col-span-2">
                                <Label htmlFor="tags">Tags</Label>
                                <Input
                                    id="tags"
                                    value={tags}
                                    onChange={(e) => setTags(e.target.value)}
                                    placeholder="e.g. Array, String, C++20, Concurrency"
                                />
                            </div>

                            <div className="space-y-1.5 sm:col-span-2">
                                <Label htmlFor="summary">One-Sentence Summary *</Label>
                                <Input
                                    id="summary"
                                    value={summary}
                                    onChange={(e) => setSummary(e.target.value)}
                                    placeholder="e.g. Implement FizzBuzz using a C++ class method returning a string vector."
                                    required
                                />
                            </div>

                            <div className="space-y-1.5">
                                <Label>Evaluation Model</Label>
                                <div className="flex gap-2">
                                    <button
                                        type="button"
                                        onClick={() => setIoMode('harness')}
                                        className={cn(
                                            'flex-1 rounded-sm border py-1.5 font-mono text-[11px]',
                                            ioMode === 'harness'
                                                ? 'border-foreground/40 bg-foreground/5 text-foreground'
                                                : 'border-border/80 bg-muted/20 text-muted-foreground',
                                        )}
                                    >
                                        harness (C++ class API)
                                    </button>
                                    <button
                                        type="button"
                                        onClick={() => setIoMode('stdio')}
                                        className={cn(
                                            'flex-1 rounded-sm border py-1.5 font-mono text-[11px]',
                                            ioMode === 'stdio'
                                                ? 'border-foreground/40 bg-foreground/5 text-foreground'
                                                : 'border-border/80 bg-muted/20 text-muted-foreground',
                                        )}
                                    >
                                        stdio (stdin/stdout)
                                    </button>
                                </div>
                            </div>

                            <div className="flex items-center gap-3">
                                <div className="flex-1 space-y-1.5">
                                    <Label>Timeout (ms)</Label>
                                    <Input
                                        type="number"
                                        value={timeLimitMs}
                                        onChange={(e) => setTimeLimitMs(Number(e.target.value) || 2000)}
                                        className="font-mono text-xs"
                                    />
                                </div>
                                <div className="flex-1 space-y-1.5">
                                    <Label>Memory Ceiling (MB)</Label>
                                    <Input
                                        type="number"
                                        value={memoryLimitMb}
                                        onChange={(e) => setMemoryLimitMb(Number(e.target.value) || 256)}
                                        className="font-mono text-xs"
                                    />
                                </div>
                            </div>
                        </div>
                    </section>

                    {/* SECTION 2: Problem Task & Technical Constraints */}
                    <section className="space-y-4 rounded-lg border border-border/90 bg-card p-5">
                        <div className="flex items-center gap-2 border-b border-border/70 pb-3 text-sm font-semibold text-foreground">
                            <ListChecks className="size-4 text-muted-foreground" />
                            <span>2. Problem Task &amp; Technical Constraints</span>
                        </div>

                        <div className="space-y-4">
                            <div className="space-y-1.5">
                                <Label htmlFor="description">
                                    Description Paragraphs *{' '}
                                    <span className="text-[11px] text-muted-foreground">
                                        (Separate paragraphs with a blank line)
                                    </span>
                                </Label>
                                <textarea
                                    id="description"
                                    value={description}
                                    onChange={(e) => setDescription(e.target.value)}
                                    rows={5}
                                    placeholder="Read an integer n and generate the FizzBuzz sequence from 1 to n.&#10;&#10;For numbers divisible by 15, produce 'FizzBuzz'. For multiples of 3, produce 'Fizz'. For multiples of 5, produce 'Buzz'."
                                    className="w-full rounded-sm border border-input bg-background p-3 text-xs leading-6 focus-visible:ring-1 focus-visible:ring-ring focus-visible:outline-none sm:text-sm"
                                    required
                                />
                            </div>

                            <div className="space-y-1.5">
                                <Label htmlFor="constraints">
                                    Technical Constraints{' '}
                                    <span className="text-[11px] text-muted-foreground">
                                        (One per line)
                                    </span>
                                </Label>
                                <textarea
                                    id="constraints"
                                    value={constraints}
                                    onChange={(e) => setConstraints(e.target.value)}
                                    rows={3}
                                    placeholder="0 <= n <= 100000&#10;Zero dynamic allocations on hot path"
                                    className="w-full rounded-sm border border-input bg-background p-3 font-mono text-xs leading-6 focus-visible:ring-1 focus-visible:ring-ring focus-visible:outline-none"
                                />
                            </div>
                        </div>
                    </section>

                    {/* SECTION 3: C++20 Starter Template */}
                    <section className="space-y-4 rounded-lg border border-border/90 bg-card p-5">
                        <div className="flex items-center justify-between border-b border-border/70 pb-3">
                            <div className="flex items-center gap-2 text-sm font-semibold text-foreground">
                                <FileCode className="size-4 text-muted-foreground" />
                                <span>3. Student starter code (`solution.cpp`)</span>
                            </div>
                            <Badge variant="outline" className="rounded-sm font-mono text-[10px] tracking-wide uppercase">
                                Editable in workspace
                            </Badge>
                        </div>

                        <p className="text-xs text-muted-foreground">
                            Boilerplate provided to the student in their workspace. Must declare the target class or functions.
                        </p>
                        <textarea
                            value={starterCode}
                            onChange={(e) => setStarterCode(e.target.value)}
                            rows={8}
                            spellCheck={false}
                            className="w-full rounded-sm border border-slate-800 bg-slate-950 p-3.5 font-mono text-xs leading-6 text-slate-100 focus-visible:ring-1 focus-visible:ring-emerald-600 focus-visible:outline-none sm:text-sm"
                            required
                        />
                    </section>

                    {/* SECTION 4: Judge Reference Solution */}
                    <section className="space-y-4 rounded-lg border border-border/90 bg-card p-5">
                        <div className="flex items-center justify-between border-b border-border/70 pb-3">
                            <div className="flex items-center gap-2 text-sm font-semibold text-foreground">
                                <Shield className="size-4 text-muted-foreground" />
                                <span>4. Authoritative judge reference (`tests/judge_solution.hpp`)</span>
                            </div>
                            <Badge variant="outline" className="rounded-sm border-amber-600/30 bg-amber-600/10 font-mono text-[10px] tracking-wide text-amber-800 uppercase dark:text-amber-400">
                                Secret reference
                            </Badge>
                        </div>

                        <p className="text-xs text-muted-foreground">
                            The gold standard implementation wrapped in `namespace judge`. Test assertions compare user return values directly against this reference.
                        </p>
                        <textarea
                            value={judgeSolution}
                            onChange={(e) => setJudgeSolution(e.target.value)}
                            rows={10}
                            spellCheck={false}
                            className="w-full rounded-sm border border-slate-800 bg-slate-950 p-3.5 font-mono text-xs leading-6 text-slate-100 focus-visible:ring-1 focus-visible:ring-emerald-600 focus-visible:outline-none sm:text-sm"
                            required
                        />
                    </section>

                    {/* SECTION 5: Individual C++ Assertion Test Cases (THE CORE ARCHITECTURE) */}
                    <section className="space-y-4 rounded-lg border border-border/90 bg-card p-5">
                        <div className="flex flex-wrap items-center justify-between gap-3 border-b border-border/70 pb-3">
                            <div className="space-y-0.5">
                                <div className="flex items-center gap-2 text-sm font-semibold text-foreground">
                                    <FlaskConical className="size-4 text-muted-foreground" />
                                    <span>5. Individual assertion cases (`problem_test_cases`)</span>
                                </div>
                                <p className="text-xs text-muted-foreground">
                                    Isolated C++ programs evaluated one-by-one against user code.
                                </p>
                            </div>

                            <div className="flex items-center gap-2">
                                <Button
                                    type="button"
                                    variant="outline"
                                    size="sm"
                                    onClick={handleAutoBalance}
                                    className="h-7 text-xs"
                                >
                                    Auto-Balance 100 pts
                                </Button>
                                <Button
                                    type="button"
                                    variant="outline"
                                    size="sm"
                                    onClick={() => addTestCase(false)}
                                    className="h-7 gap-1 text-xs"
                                >
                                    <Unlock className="size-3" />
                                    <span>Add Public</span>
                                </Button>
                                <Button
                                    type="button"
                                    size="sm"
                                    onClick={() => addTestCase(true)}
                                    className="h-7 gap-1 text-xs"
                                >
                                    <Lock className="size-3" />
                                    <span>Add Hidden</span>
                                </Button>
                            </div>
                        </div>

                        {/* Grade Points & Test Counts Summary */}
                        <div className={cn(
                            'flex flex-wrap items-center justify-between gap-2 rounded-sm border px-3.5 py-2.5 font-mono text-[11px]',
                            isBalanced
                                ? 'border-emerald-600/25 bg-emerald-600/5 text-emerald-800 dark:text-emerald-400'
                                : 'border-amber-600/25 bg-amber-600/5 text-amber-800 dark:text-amber-400',
                        )}>
                            <div className="flex items-center gap-3">
                                <span>
                                    Total grade points: <strong className="text-sm font-semibold tabular-nums">{totalPoints}</strong> / 100
                                </span>
                                <span className="text-muted-foreground">·</span>
                                <span>{publicCount} public</span>
                                <span className="text-muted-foreground">·</span>
                                <span>{hiddenCount} hidden</span>
                            </div>

                            <div>
                                {isBalanced ? (
                                    <span className="inline-flex items-center gap-1">
                                        <CheckCircle2 className="size-3.5" /> Balanced
                                    </span>
                                ) : (
                                    <span className="inline-flex items-center gap-1">
                                        <AlertCircle className="size-3.5" /> Points do not equal 100
                                    </span>
                                )}
                            </div>
                        </div>

                        {/* Test Cases List */}
                        <div className="space-y-4">
                            {testCases.map((tc, index) => (
                                <article
                                    key={index}
                                    className="space-y-3 rounded-md border border-border/90 bg-background/40 p-4"
                                >
                                    {/* Test Case Header */}
                                    <div className="flex flex-wrap items-center justify-between gap-2 border-b border-border/60 pb-2.5">
                                        <div className="flex items-center gap-2">
                                            <Badge
                                                variant="outline"
                                                className={cn(
                                                    'rounded-sm font-mono text-[10px] tracking-wide uppercase',
                                                    tc.hidden
                                                        ? 'border-amber-600/40 bg-amber-600/10 text-amber-800 dark:text-amber-400'
                                                        : 'border-emerald-600/40 bg-emerald-600/10 text-emerald-800 dark:text-emerald-400',
                                                )}
                                            >
                                                {tc.hidden ? 'Secret' : 'Public'}
                                            </Badge>

                                            <Input
                                                value={tc.case_id}
                                                onChange={(e) =>
                                                    updateTestCase(index, 'case_id', e.target.value)
                                                }
                                                placeholder="case_id (e.g. p1_one)"
                                                className="h-7 w-28 font-mono text-xs"
                                                required
                                            />

                                            <Input
                                                value={tc.name}
                                                onChange={(e) =>
                                                    updateTestCase(index, 'name', e.target.value)
                                                }
                                                placeholder="Human label (e.g. n=1)"
                                                className="h-7 w-44 text-xs font-medium"
                                                required
                                            />
                                        </div>

                                        <div className="flex items-center gap-2.5">
                                            {/* Failure Group */}
                                            <select
                                                value={tc.group}
                                                onChange={(e) =>
                                                    updateTestCase(index, 'group', e.target.value as TestGroup)
                                                }
                                                className="h-7 rounded-sm border border-border/90 bg-background px-2 font-mono text-[11px]"
                                            >
                                                {testGroups.map((g) => (
                                                    <option key={g} value={g}>
                                                        [{g}]
                                                    </option>
                                                ))}
                                            </select>

                                            {/* Points */}
                                            <div className="flex items-center gap-1">
                                                <Label className="text-xs">Points:</Label>
                                                <Input
                                                    type="number"
                                                    min={0}
                                                    max={100}
                                                    value={tc.points}
                                                    onChange={(e) =>
                                                        updateTestCase(
                                                            index,
                                                            'points',
                                                            parseInt(e.target.value) || 0,
                                                        )
                                                    }
                                                    className="h-7 w-16 text-right font-mono text-xs"
                                                    required
                                                />
                                            </div>

                                            {/* Hidden toggle */}
                                            <label className="flex items-center gap-1 text-xs text-muted-foreground cursor-pointer select-none">
                                                <input
                                                    type="checkbox"
                                                    checked={tc.hidden}
                                                    onChange={(e) =>
                                                        updateTestCase(index, 'hidden', e.target.checked)
                                                    }
                                                    className="size-3.5"
                                                />
                                                <span>Hidden</span>
                                            </label>

                                            {testCases.length > 1 && (
                                                <button
                                                    type="button"
                                                    onClick={() => removeTestCase(index)}
                                                    className="text-muted-foreground hover:text-rose-500 transition-colors ml-1"
                                                    title="Remove test case"
                                                >
                                                    <Trash2 className="size-3.5" />
                                                </button>
                                            )}
                                        </div>
                                    </div>

                                    {/* C++ Test Code Area */}
                                    <div className="space-y-1">
                                        <div className="flex items-center justify-between text-[11px] font-mono text-muted-foreground">
                                            <span>tests/{tc.hidden ? 'hidden' : 'public'}/test_{tc.case_id}.cpp</span>
                                            <span>Isolated C++ assertion program</span>
                                        </div>
                                        <textarea
                                            value={tc.test_code_cpp}
                                            onChange={(e) =>
                                                updateTestCase(index, 'test_code_cpp', e.target.value)
                                            }
                                            rows={6}
                                            spellCheck={false}
                                            className="w-full rounded-sm border border-slate-800 bg-slate-950 p-2.5 font-mono text-xs leading-5 text-slate-100 focus-visible:ring-1 focus-visible:ring-emerald-600 focus-visible:outline-none"
                                            required
                                        />
                                    </div>
                                </article>
                            ))}
                        </div>
                    </section>

                    {/* SECTION 6: Assertion Framework Header */}
                    <section className="space-y-4 rounded-lg border border-border/90 bg-card p-5">
                        <div className="flex items-center justify-between border-b border-border/70 pb-3">
                            <div className="flex items-center gap-2 text-sm font-semibold text-foreground">
                                <Terminal className="size-4 text-muted-foreground" />
                                <span>6. Framework header (`tests/judge_test.h`)</span>
                            </div>
                            <span className="font-mono text-xs text-muted-foreground">
                                Shared Assertion Macros
                            </span>
                        </div>

                        <textarea
                            value={judgeTestH}
                            onChange={(e) => setJudgeTestH(e.target.value)}
                            rows={6}
                            spellCheck={false}
                            className="w-full rounded-sm border border-slate-800 bg-slate-950 p-3 font-mono text-xs leading-5 text-slate-100 focus-visible:ring-1 focus-visible:ring-emerald-600 focus-visible:outline-none"
                        />
                    </section>

                        <div className="flex flex-wrap items-center justify-between gap-4 border-t border-border/80 pt-4">
                            <Button type="button" variant="outline" asChild>
                                <Link href={problemsetRoute()}>Cancel</Link>
                            </Button>

                            <Button
                                type="submit"
                                disabled={isSubmitting}
                                className="gap-2"
                            >
                                {isSubmitting ? (
                                    <Spinner className="size-4" />
                                ) : (
                                    <Send className="size-4" />
                                )}
                                <span>Publish problem</span>
                            </Button>
                        </div>
                    </div>

                    <aside className="space-y-4 xl:sticky xl:top-20">
                        <section className="overflow-hidden rounded-lg border border-border/90 bg-card">
                            <div className="flex items-center justify-between border-b border-border/80 bg-muted/40 px-4 py-2.5">
                                <span className="meta-label">Publish</span>
                                <span className="font-mono text-[10px] text-muted-foreground">
                                    #{nextNumber}
                                </span>
                            </div>

                            <div className="space-y-3 p-4">
                                <div className="flex items-end justify-between">
                                    <div>
                                        <p className="meta-label">Grade points</p>
                                        <p
                                            className={cn(
                                                'display-title text-3xl tabular-nums',
                                                isBalanced
                                                    ? 'text-foreground'
                                                    : 'text-amber-700 dark:text-amber-400',
                                            )}
                                        >
                                            {totalPoints}
                                            <span className="text-base text-muted-foreground">
                                                /100
                                            </span>
                                        </p>
                                    </div>
                                    <div className="text-right font-mono text-[10px] text-muted-foreground">
                                        <p>{publicCount} public</p>
                                        <p>{hiddenCount} hidden</p>
                                    </div>
                                </div>

                                <div className="h-1 w-full overflow-hidden rounded-full bg-muted">
                                    <div
                                        className={cn(
                                            'h-full',
                                            isBalanced
                                                ? 'bg-emerald-600'
                                                : 'bg-amber-500',
                                        )}
                                        style={{
                                            width: `${Math.min(totalPoints, 100)}%`,
                                        }}
                                    />
                                </div>

                                <p
                                    className={cn(
                                        'text-[11px] leading-5',
                                        isBalanced
                                            ? 'text-muted-foreground'
                                            : 'text-amber-700 dark:text-amber-400',
                                    )}
                                >
                                    {isBalanced
                                        ? 'Points sum to 100. The suite is ready to grade.'
                                        : 'Per-case points must sum to exactly 100 before publishing.'}
                                </p>

                                <Button
                                    type="submit"
                                    disabled={isSubmitting}
                                    className="w-full gap-2"
                                >
                                    {isSubmitting ? (
                                        <Spinner className="size-4" />
                                    ) : (
                                        <Send className="size-4" />
                                    )}
                                    Publish problem
                                </Button>
                            </div>
                        </section>

                        <section className="overflow-hidden rounded-lg border border-border/90 bg-card">
                            <div className="border-b border-border/80 bg-muted/40 px-4 py-2.5">
                                <span className="meta-label">Spec checklist</span>
                            </div>
                            <div className="divide-y divide-border/70">
                                {[
                                    ['Title + slug', title.trim() !== '' && id.trim() !== ''],
                                    ['Summary', summary.trim() !== ''],
                                    ['Description', description.trim() !== ''],
                                    ['Starter solution.cpp', starterCode.trim() !== ''],
                                    ['Judge reference', judgeSolution.trim() !== ''],
                                    ['Assertion header', judgeTestH.trim() !== ''],
                                    [
                                        'Cases sum to 100',
                                        isBalanced && testCases.length > 0,
                                    ],
                                    [
                                        'Hidden cases present',
                                        hiddenCount > 0,
                                    ],
                                ].map(([label, done]) => (
                                    <div
                                        key={String(label)}
                                        className="flex items-center gap-2 px-4 py-1.5 text-[11px]"
                                    >
                                        {done ? (
                                            <CheckCircle2 className="size-3.5 shrink-0 text-emerald-600 dark:text-emerald-400" />
                                        ) : (
                                            <AlertCircle className="size-3.5 shrink-0 text-muted-foreground/50" />
                                        )}
                                        <span
                                            className={cn(
                                                done
                                                    ? 'text-foreground'
                                                    : 'text-muted-foreground',
                                            )}
                                        >
                                            {label}
                                        </span>
                                    </div>
                                ))}
                            </div>
                        </section>

                        <div className="rounded-lg border border-border/90 bg-card p-4">
                            <p className="meta-label flex items-center gap-1.5">
                                <Lock className="size-3" />
                                Zero-leak rule
                            </p>
                            <p className="mt-1.5 text-[11px] leading-5 text-muted-foreground">
                                Hidden test code is stored server-side and never
                                serialised into the problem page. Only the case
                                name, group, and points reach the browser.
                            </p>
                        </div>
                    </aside>
                </form>
            </div>
        </>
    );
}
