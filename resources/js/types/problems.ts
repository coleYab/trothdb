export type Difficulty = 'Easy' | 'Medium' | 'Hard';

export type ProblemSummary = {
    id: string;
    number: number;
    title: string;
    difficulty: Difficulty;
    acceptance_rate: number;
    tags: string[];
    summary: string;
};

export type ProblemExample = {
    input: string;
    output: string;
    explanation?: string;
};

export type ProblemSampleTest = {
    name: string;
    input: string;
    expected: string;
    hidden: boolean;
};

export type ProblemTesting = {
    judge: string;
    time_limit: string;
    memory_limit: string;
    languages: string[];
    notes: string[];
    sample_tests: ProblemSampleTest[];
};

export type ProblemEditorial = {
    insight: string;
    steps: string[];
    time_complexity: string;
    space_complexity: string;
};

export type Problem = ProblemSummary & {
    description: string[];
    examples: ProblemExample[];
    constraints: string[];
    follow_up?: string;
    editorial: ProblemEditorial;
    starter_code: Record<string, string>;
    testing: ProblemTesting;
    hints: string[];
};
