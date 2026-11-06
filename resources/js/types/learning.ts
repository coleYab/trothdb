import type { Difficulty, Problem, ProblemSummary } from './problems';

export type TrackLevel = 'Beginner' | 'Intermediate' | 'Advanced';

export type Challenge = Problem;
export type ChallengeSummary = ProblemSummary;

export type LessonBlock =
    | { type: 'paragraph'; text: string }
    | { type: 'code'; title: string; code: string }
    | { type: 'list'; title?: string; items: string[] }
    | {
          type: 'callout';
          variant: 'info' | 'warning' | 'tip';
          title: string;
          text: string;
      };

export type LessonQuiz = {
    question: string;
    options: string[];
    answer: number;
    explanation: string;
};

export type LessonDiscussion = {
    author: string;
    role: string;
    body: string;
    votes: number;
};

export type Lesson = {
    slug: string;
    title: string;
    summary: string;
    minutes: number;
    blocks: LessonBlock[];
    quiz: LessonQuiz;
    assembly: string;
    discussion: LessonDiscussion[];
};

export type SyllabusItem = {
    id: string;
    type: 'lesson' | 'project' | 'assessment';
    title: string;
    lessonSlug?: string;
    minutes: number;
    completed: boolean;
};

export type LearnModule = {
    title: string;
    summary: string;
    items: SyllabusItem[];
};

export type Track = {
    slug: string;
    title: string;
    level: TrackLevel;
    hours: number;
    summary: string;
    description: string;
    progress: number;
    continueLesson: string;
    prerequisites: string[];
    modules: LearnModule[];
    lessons: Lesson[];
};

export type DailyEntry = {
    date: string;
    slug: string;
    challengeSlug: string;
    title: string;
    difficulty: Difficulty;
    topics: string[];
    solved: boolean;
    participants: number;
};
