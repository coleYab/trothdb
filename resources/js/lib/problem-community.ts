import type { Problem, ProblemSummary } from '@/types';

export type SubmissionStatus =
    | 'Accepted'
    | 'Wrong Answer'
    | 'Time Limit Exceeded'
    | 'Memory Limit Exceeded'
    | 'Runtime Error'
    | 'Compile Error';

export type SubmissionRow = {
    rank: number;
    user: string;
    language: string;
    runtime: number;
    memory: number;
    status: SubmissionStatus;
    submitted: string;
};

export type StatusSlice = {
    status: SubmissionStatus;
    count: number;
    percent: number;
};

export type RuntimeBucket = {
    label: string;
    count: number;
    percent: number;
};

export type LanguageSlice = {
    name: string;
    count: number;
    percent: number;
};

export type SubmissionOverview = {
    total: number;
    accepted: number;
    acceptance: number;
    medianRuntime: number;
    bestRuntime: number;
    statuses: StatusSlice[];
    runtimeBuckets: RuntimeBucket[];
    languages: LanguageSlice[];
    top: SubmissionRow[];
};

export type ProblemComment = {
    id: string;
    author: string;
    initials: string;
    time: string;
    body: string;
    votes: number;
    reply?: {
        author: string;
        initials: string;
        time: string;
        body: string;
        votes: number;
    };
};

export type StandingRow = {
    rank: number;
    user: string;
    initials: string;
    score: number;
    solved: number;
    penalty: number;
    lastAccepted: string;
};

const submissionHandles = [
    'aether',
    'bytefox',
    'dp_wizard',
    'ecnerwala',
    'graphite',
    'maroonrk',
    'nibble',
    'petr',
    'Radewoosh',
    'segmenteer',
    'tourist',
    'Um_nik',
];

const commentAuthors = [
    'Priya Raman',
    'Daniel Kim',
    'Sofia Almeida',
    'Marcus Chen',
    'Aisha Bello',
    'Noah Fischer',
];

const standingHandles = [
    'aether',
    'bytefox',
    'dp_wizard',
    'graphite',
    'maroonrk',
    'nibble',
    'segmenteer',
    'tourist',
];

const submissionLanguages = ['C++ 20', 'Python 3.12', 'JavaScript (Node 22)'];

function hashString(value: string): number {
    let hash = 2166136261;

    for (let index = 0; index < value.length; index += 1) {
        hash ^= value.charCodeAt(index);
        hash = Math.imul(hash, 16777619);
    }

    return hash >>> 0;
}

function createRandom(seed: number): () => number {
    let state = seed >>> 0;

    return () => {
        state = (Math.imul(state, 1664525) + 1013904223) >>> 0;

        return state / 4294967296;
    };
}

function initials(name: string): string {
    return name
        .split(/[\s_]+/)
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part[0]?.toUpperCase() ?? '')
        .join('');
}

function clampPercent(value: number): number {
    return Math.max(0, Math.min(100, value));
}

function averageRuntime(problem: ProblemSummary): number {
    if (problem.difficulty === 'Easy') {
        return 4;
    }

    if (problem.difficulty === 'Medium') {
        return 11;
    }

    return 27;
}

function runtimeBucketLabels(problem: ProblemSummary): string[] {
    if (problem.difficulty === 'Easy') {
        return ['0–2 ms', '2–5 ms', '5–10 ms', '10–20 ms', '20+ ms'];
    }

    if (problem.difficulty === 'Medium') {
        return ['0–5 ms', '5–12 ms', '12–25 ms', '25–50 ms', '50+ ms'];
    }

    return ['0–10 ms', '10–25 ms', '25–50 ms', '50–100 ms', '100+ ms'];
}

export function buildSubmissionOverview(
    problem: ProblemSummary,
): SubmissionOverview {
    const random = createRandom(hashString(`${problem.id}:submissions`));
    const total = Math.round((4200 + random() * 12800) / 10) * 10;
    const acceptance = problem.acceptance_rate;
    const accepted = Math.round((total * acceptance) / 100);
    const failed = total - accepted;

    const failureWeights: Array<[SubmissionStatus, number]> = [
        ['Wrong Answer', 0.52],
        ['Time Limit Exceeded', 0.21],
        ['Runtime Error', 0.12],
        ['Memory Limit Exceeded', 0.08],
        ['Compile Error', 0.07],
    ];

    let allocated = 0;
    const statuses: StatusSlice[] = [
        {
            status: 'Accepted',
            count: accepted,
            percent: clampPercent(acceptance),
        },
    ];

    failureWeights.forEach(([status, weight], index) => {
        const count =
            index === failureWeights.length - 1
                ? Math.max(0, failed - allocated)
                : Math.round(failed * weight);

        allocated += count;

        statuses.push({
            status,
            count,
            percent: clampPercent((count / total) * 100),
        });
    });

    const bucketWeights = [0.1, 0.25, 0.34, 0.21, 0.1];
    let bucketAllocated = 0;
    const runtimeBuckets = runtimeBucketLabels(problem).map((label, index) => {
        const count =
            index === bucketWeights.length - 1
                ? Math.max(0, accepted - bucketAllocated)
                : Math.round(accepted * bucketWeights[index]);

        bucketAllocated += count;

        return {
            label,
            count,
            percent: accepted === 0 ? 0 : (count / accepted) * 100,
        };
    });

    const languageWeights = [0.47, 0.31, 0.22];
    let languageAllocated = 0;
    const languages = submissionLanguages.map((name, index) => {
        const count =
            index === languageWeights.length - 1
                ? Math.max(0, total - languageAllocated)
                : Math.round(total * languageWeights[index]);

        languageAllocated += count;

        return {
            name,
            count,
            percent: (count / total) * 100,
        };
    });

    const baseline = averageRuntime(problem);
    const handleOffset = Math.floor(random() * submissionHandles.length);

    const top = submissionHandles
        .map((user, index) => {
            const runtime =
                Math.round(
                    (baseline +
                        random() * baseline * 1.8 +
                        (index % 5) * (baseline / 7)) *
                        10,
                ) / 10;

            return {
                rank: 0,
                user,
                language:
                    submissionLanguages[
                        (index + handleOffset) % submissionLanguages.length
                    ],
                runtime,
                memory:
                    Math.round(
                        (problem.difficulty === 'Hard'
                            ? 22 + random() * 30
                            : problem.difficulty === 'Medium'
                              ? 14 + random() * 22
                              : 8 + random() * 14) * 10,
                    ) / 10,
                status: 'Accepted' as SubmissionStatus,
                submitted: [
                    '2 min ago',
                    '6 min ago',
                    '11 min ago',
                    '18 min ago',
                ][index % 4],
            };
        })
        .sort((first, second) => first.runtime - second.runtime)
        .slice(0, 8)
        .map((row, index) => ({ ...row, rank: index + 1 }));

    return {
        total,
        accepted,
        acceptance,
        medianRuntime: Math.round(baseline * 1.65 * 10) / 10,
        bestRuntime: top[0]?.runtime ?? baseline,
        statuses,
        runtimeBuckets,
        languages,
        top,
    };
}

export function buildComments(problem: Problem): ProblemComment[] {
    const random = createRandom(hashString(`${problem.id}:comments`));
    const tag = problem.tags[0]?.toLowerCase() ?? 'algorithm';
    const nextTag = problem.tags[1]?.toLowerCase() ?? 'edge case';
    const note =
        problem.testing.notes[0] ??
        'the hidden tests are stricter than the samples.';

    const bodies = [
        `The ${tag} insight was the part I kept missing. I wrote the brute force first, and the optimisation finally clicked after rereading the editorial.`,
        `Is the ${problem.testing.time_limit} limit strict for Python? My O(n log n) solution passes the samples, but I am worried about the hidden cases.`,
        `Small gotcha worth testing: ${note}`,
        `The samples do not cover every edge case. I would add a case where the answer is at the very end of the input before submitting.`,
    ];

    const times = ['2 hours ago', '5 hours ago', 'yesterday', '2 days ago'];
    const votes = [24, 11, 7, 3];

    const comments: ProblemComment[] = bodies.map((body, index) => {
        const author =
            commentAuthors[
                (index + Math.floor(random() * 3)) % commentAuthors.length
            ];

        return {
            id: `${problem.id}-comment-${index}`,
            author,
            initials: initials(author),
            time: times[index],
            body,
            votes: votes[index],
        };
    });

    const replyAuthor =
        commentAuthors[(Math.floor(random() * 6) + 3) % commentAuthors.length];

    comments[1].reply = {
        author: replyAuthor,
        initials: initials(replyAuthor),
        time: '4 hours ago',
        body: `The ${
            nextTag || 'constraint'
        } bound is the part that matters. If your complexity fits the largest input, the language overhead is usually fine.`,
        votes: 6,
    };

    return comments;
}

export function buildStandings(problem: Problem): StandingRow[] {
    const random = createRandom(hashString(`${problem.id}:standings`));
    const difficultyPenalty =
        problem.difficulty === 'Hard'
            ? 0
            : problem.difficulty === 'Medium'
              ? 80
              : 160;

    return standingHandles
        .map((user) => ({
            rank: 0,
            user,
            initials: initials(user),
            score:
                3600 -
                difficultyPenalty -
                Math.floor(random() * 260) -
                standingHandles.indexOf(user) * 62,
            solved: 8 - Math.floor(standingHandles.indexOf(user) / 2),
            penalty: 120 + Math.floor(random() * 360),
            lastAccepted: ['2m', '6m', '9m', '14m', '22m', '31m', '44m', '58m'][
                standingHandles.indexOf(user)
            ],
        }))
        .sort((first, second) => second.score - first.score)
        .map((row, index) => ({ ...row, rank: index + 1 }));
}
