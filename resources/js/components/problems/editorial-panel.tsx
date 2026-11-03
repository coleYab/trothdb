import { BookOpen, Clock3 } from 'lucide-react';
import { Callout } from '@/components/callout';
import type { Problem } from '@/types';
import { PanelHeader } from '@/components/problems/panel-ui';

export function EditorialPanel({ problem }: { problem: Problem }) {
    const editorial = problem.editorial;

    return (
        <div className="space-y-6">
            <PanelHeader
                icon={BookOpen}
                title="Editorial"
                description="A concise walkthrough of the intended approach, including the key insight and complexity."
                badge="Community editorial"
            />

            <Callout variant="tip" title="Key insight">
                {editorial.insight}
            </Callout>

            <div className="grid gap-3 sm:grid-cols-2">
                <ComplexityCard
                    label="Time complexity"
                    value={editorial.time_complexity}
                    description="Upper bound for the intended solution."
                />
                <ComplexityCard
                    label="Space complexity"
                    value={editorial.space_complexity}
                    description="Extra memory beyond the input."
                />
            </div>

            <section className="space-y-3">
                <h3 className="text-base font-semibold">Approach</h3>
                <ol className="space-y-3">
                    {editorial.steps.map((step, index) => (
                        <li
                            key={step}
                            className="flex gap-3 rounded-lg border bg-card/40 p-4"
                        >
                            <span className="flex size-6 shrink-0 items-center justify-center rounded-full bg-primary/10 font-mono text-xs font-semibold text-primary">
                                {index + 1}
                            </span>
                            <p className="text-sm leading-7 text-foreground/90">
                                {step}
                            </p>
                        </li>
                    ))}
                </ol>
            </section>

            <section className="space-y-3">
                <h3 className="text-base font-semibold">Walkthrough</h3>
                <div className="rounded-xl border bg-card/40 p-4">
                    <p className="text-sm leading-7 text-muted-foreground">
                        Use the first sample to sanity-check the implementation.
                    </p>
                    <div className="mt-3 grid gap-3 sm:grid-cols-2">
                        <DetailCode
                            label="Input"
                            value={problem.examples[0]?.input ?? ''}
                        />
                        <DetailCode
                            label="Expected output"
                            value={problem.examples[0]?.output ?? ''}
                        />
                    </div>
                    {problem.examples[0]?.explanation && (
                        <p className="mt-3 text-sm leading-7 text-muted-foreground">
                            <span className="font-medium text-foreground">
                                Why:{' '}
                            </span>
                            {problem.examples[0].explanation}
                        </p>
                    )}
                </div>
            </section>

            <Callout variant="info" title="Try it">
                Open the <span className="font-medium">Description</span> tab,
                copy the starter code, and implement the steps above before
                running the sample tests.
            </Callout>
        </div>
    );
}

function ComplexityCard({
    label,
    value,
    description,
}: {
    label: string;
    value: string;
    description: string;
}) {
    return (
        <div className="rounded-xl border bg-card p-4">
            <div className="flex items-center gap-2">
                <Clock3 className="size-4 text-muted-foreground" />
                <p className="text-[11px] font-medium tracking-wider text-muted-foreground uppercase">
                    {label}
                </p>
            </div>
            <p className="mt-2 font-mono text-2xl font-semibold tabular-nums">
                {value}
            </p>
            <p className="mt-1 text-xs text-muted-foreground">{description}</p>
        </div>
    );
}

function DetailCode({ label, value }: { label: string; value: string }) {
    return (
        <div className="space-y-1.5">
            <p className="text-[11px] font-semibold tracking-wider text-muted-foreground uppercase">
                {label}
            </p>
            <pre className="overflow-x-auto rounded-lg border border-slate-800 bg-slate-950 px-3 py-2.5 font-mono text-xs leading-6 text-slate-100">
                <code>{value}</code>
            </pre>
        </div>
    );
}
