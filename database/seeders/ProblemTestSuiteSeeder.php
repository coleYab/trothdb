<?php

namespace Database\Seeders;

use App\Models\Problem;
use App\Models\ProblemAsset;
use App\Models\ProblemTestCase;
use Illuminate\Database\Seeder;

/**
 * Attaches Specification 2.0 test suites to the seeded problems.
 *
 * Every problem receives four standalone C++ assertion programs (two public,
 * two hidden) whose weighted points sum to 100, plus the shared assertion
 * header and the authoritative judge reference used by the CLI bundle.
 */
class ProblemTestSuiteSeeder extends Seeder
{
    private const JUDGE_TEST_H = <<<'CPP'
#pragma once
#include <cmath>
#include <cstddef>
#include <cstdint>
#include <cstdlib>
#include <cstring>
#include <iostream>
#include <string>
#include <utility>
#include <vector>

#define ASSERT_TRUE(cond, msg) \
    if (!(cond)) { \
        std::cerr << "Assertion failed [" << (msg) << "]: expected true" << std::endl; \
        std::exit(1); \
    }

#define ASSERT_FALSE(cond, msg) \
    if ((cond)) { \
        std::cerr << "Assertion failed [" << (msg) << "]: expected false" << std::endl; \
        std::exit(1); \
    }

#define ASSERT_EQ(a, b, msg) \
    if ((a) != (b)) { \
        std::cerr << "Assertion failed [" << (msg) << "]: " << (a) << " != " << (b) << std::endl; \
        std::exit(1); \
    }

#define ASSERT_NEAR(a, b, eps, msg) \
    if (std::abs((a) - (b)) > (eps)) { \
        std::cerr << "Assertion failed [" << (msg) << "]: " << (a) << " != " << (b) << std::endl; \
        std::exit(1); \
    }

#define COMPARE_ARRAYS(actual, expected, msg) \
    if ((actual).size() != (expected).size()) { \
        std::cerr << "Size mismatch [" << (msg) << "]: expected " << (expected).size() \
                  << ", got " << (actual).size() << std::endl; \
        std::exit(1); \
    } \
    for (std::size_t i = 0; i < (actual).size(); ++i) { \
        if ((actual)[i] != (expected)[i]) { \
            std::cerr << "Mismatch at index " << i << " [" << (msg) << "]" << std::endl; \
            std::exit(1); \
        } \
    }
CPP;

    public function run(): void
    {
        foreach ($this->suites() as $problemId => $suite) {
            $problem = Problem::find($problemId);

            if (! $problem) {
                continue;
            }

            $this->asset($problemId, 'tests/judge_test.h', 'framework_header', self::JUDGE_TEST_H, false);
            $this->asset($problemId, 'tests/judge_solution.hpp', 'reference_solution', $suite['reference'], true);

            foreach ($suite['cases'] as $index => $case) {
                $isHidden = $case['hidden'];
                $folder = $isHidden ? 'hidden' : 'public';
                $code = $this->caseCode($case['body'], $case['extra'] ?? '');

                ProblemTestCase::updateOrCreate(
                    [
                        'problem_id' => $problemId,
                        'case_id' => $case['case_id'],
                    ],
                    [
                        'name' => $case['name'],
                        'group' => $case['group'],
                        'file_path' => "tests/{$folder}/test_{$case['case_id']}.cpp",
                        'test_code_cpp' => $code,
                        'points' => $case['points'],
                        'time_limit_ms' => $case['time_limit_ms'] ?? 2000,
                        'is_hidden' => $isHidden,
                        'order_index' => $index,
                    ]
                );
            }
        }
    }

    private function asset(string $problemId, string $path, string $type, string $content, bool $secret): void
    {
        ProblemAsset::updateOrCreate(
            ['problem_id' => $problemId, 'file_path' => $path],
            [
                'file_type' => $type,
                'content' => $content,
                'sha256_hash' => hash('sha256', $content),
                'is_secret' => $secret,
            ]
        );
    }

    private function caseCode(string $body, string $extra = ''): string
    {
        $extraBlock = $extra === '' ? '' : $extra."\n\n";

        return "#include \"judge_test.h\"\n"
            .$extraBlock
            ."#define arena user_arena\n"
            ."#define Solution UserSolution\n"
            ."#define LRUCache UserLRUCache\n"
            ."#include \"solution.cpp\"\n"
            ."#undef Solution\n"
            ."#undef LRUCache\n"
            ."#undef arena\n"
            ."#include \"judge_solution.hpp\"\n\n"
            ."int main() {\n"
            .$body."\n"
            ."    return 0;\n"
            ."}\n";
    }

    /**
     * @return array<string, array{reference: string, cases: array<int, array<string, mixed>>}>
     */
    private function suites(): array
    {
        return [
            'two-sum' => [
                'reference' => <<<'CPP'
#pragma once
#include <unordered_map>
#include <vector>

namespace judge {
class Solution {
public:
    std::vector<int> twoSum(std::vector<int>& nums, int target) {
        std::unordered_map<int, int> seen;
        for (int i = 0; i < static_cast<int>(nums.size()); ++i) {
            auto it = seen.find(target - nums[i]);
            if (it != seen.end()) {
                return {it->second, i};
            }
            seen[nums[i]] = i;
        }
        return {};
    }
};
}
CPP,
                'cases' => [
                    [
                        'case_id' => 'p1_basic', 'name' => 'Basic pair', 'group' => 'basic',
                        'points' => 20, 'hidden' => false,
                        'body' => <<<'CPP'
    std::vector<int> a{2, 7, 11, 15}, b{2, 7, 11, 15};
    COMPARE_ARRAYS(u.twoSum(a, 9), j.twoSum(b, 9), "basic pair");
CPP,
                    ],
                    [
                        'case_id' => 'p2_negative', 'name' => 'Negative values', 'group' => 'basic',
                        'points' => 25, 'hidden' => false,
                        'body' => <<<'CPP'
    std::vector<int> a{-3, 4, 3, 90}, b{-3, 4, 3, 90};
    COMPARE_ARRAYS(u.twoSum(a, 0), j.twoSum(b, 0), "negative target");
CPP,
                    ],
                    [
                        'case_id' => 'h1_duplicates', 'name' => 'Duplicate values', 'group' => 'edge',
                        'points' => 25, 'hidden' => true,
                        'body' => <<<'CPP'
    std::vector<int> a{3, 3}, b{3, 3};
    COMPARE_ARRAYS(u.twoSum(a, 6), j.twoSum(b, 6), "duplicate values");
CPP,
                    ],
                    [
                        'case_id' => 'h2_scale', 'name' => '100k element scale', 'group' => 'scale',
                        'points' => 30, 'hidden' => true,
                        'body' => <<<'CPP'
    std::vector<int> a, b;
    a.reserve(100001);
    b.reserve(100001);
    for (int i = 0; i < 100000; ++i) { a.push_back(i); b.push_back(i); }
    a.push_back(100000); b.push_back(100000);
    COMPARE_ARRAYS(u.twoSum(a, 199998), j.twoSum(b, 199998), "100k elements");
CPP,
                    ],
                ],
            ],

            'valid-parentheses' => [
                'reference' => <<<'CPP'
#pragma once
#include <string>

namespace judge {
class Solution {
public:
    bool isValid(std::string s) {
        std::string stack;
        for (char c : s) {
            if (c == '(' || c == '[' || c == '{') {
                stack.push_back(c);
            } else {
                if (stack.empty()) return false;
                char open = stack.back();
                stack.pop_back();
                if ((c == ')' && open != '(') || (c == ']' && open != '[') || (c == '}' && open != '{')) {
                    return false;
                }
            }
        }
        return stack.empty();
    }
};
}
CPP,
                'cases' => [
                    [
                        'case_id' => 'p1_single', 'name' => 'Single pair', 'group' => 'basic',
                        'points' => 20, 'hidden' => false,
                        'body' => "    ASSERT_EQ(u.isValid(\"()\"), j.isValid(\"()\"), \"single pair\");",
                    ],
                    [
                        'case_id' => 'p2_mixed', 'name' => 'Mixed bracket types', 'group' => 'basic',
                        'points' => 25, 'hidden' => false,
                        'body' => "    ASSERT_EQ(u.isValid(\"()[]{}\"), j.isValid(\"()[]{}\"), \"mixed pairs\");",
                    ],
                    [
                        'case_id' => 'h1_mismatch', 'name' => 'Mismatched nesting', 'group' => 'edge',
                        'points' => 25, 'hidden' => true,
                        'body' => "    ASSERT_EQ(u.isValid(\"(]\"), j.isValid(\"(]\"), \"mismatched\");",
                    ],
                    [
                        'case_id' => 'h2_scale', 'name' => 'Deep nesting scale', 'group' => 'scale',
                        'points' => 30, 'hidden' => true,
                        'body' => <<<'CPP'
    std::string s;
    for (int i = 0; i < 10000; ++i) s += "({[]})";
    ASSERT_EQ(u.isValid(s), j.isValid(s), "nested scale");
CPP,
                    ],
                ],
            ],

            'merge-intervals' => [
                'reference' => <<<'CPP'
#pragma once
#include <algorithm>
#include <vector>

namespace judge {
class Solution {
public:
    std::vector<std::vector<int>> merge(std::vector<std::vector<int>>& intervals) {
        std::vector<std::vector<int>> result;
        if (intervals.empty()) return result;
        std::sort(intervals.begin(), intervals.end());
        result.push_back(intervals.front());
        for (std::size_t i = 1; i < intervals.size(); ++i) {
            if (intervals[i][0] <= result.back()[1]) {
                result.back()[1] = std::max(result.back()[1], intervals[i][1]);
            } else {
                result.push_back(intervals[i]);
            }
        }
        return result;
    }
};
}
CPP,
                'cases' => [
                    [
                        'case_id' => 'p1_overlap', 'name' => 'Overlapping intervals', 'group' => 'basic',
                        'points' => 20, 'hidden' => false,
                        'body' => <<<'CPP'
    std::vector<std::vector<int>> a{{1, 3}, {2, 6}, {8, 10}, {15, 18}}, b = a;
    auto ur = u.merge(a), jr = j.merge(b);
    ASSERT_EQ(ur.size(), jr.size(), "merged count");
    for (std::size_t i = 0; i < ur.size(); ++i) COMPARE_ARRAYS(ur[i], jr[i], "interval");
CPP,
                    ],
                    [
                        'case_id' => 'p2_empty', 'name' => 'Empty input', 'group' => 'edge',
                        'points' => 25, 'hidden' => false,
                        'body' => <<<'CPP'
    std::vector<std::vector<int>> a, b;
    ASSERT_EQ(u.merge(a).size(), j.merge(b).size(), "empty input");
CPP,
                    ],
                    [
                        'case_id' => 'h1_touching', 'name' => 'Touching intervals', 'group' => 'edge',
                        'points' => 25, 'hidden' => true,
                        'body' => <<<'CPP'
    std::vector<std::vector<int>> a{{1, 4}, {4, 5}}, b = a;
    auto ur = u.merge(a), jr = j.merge(b);
    ASSERT_EQ(ur.size(), jr.size(), "touching count");
    for (std::size_t i = 0; i < ur.size(); ++i) COMPARE_ARRAYS(ur[i], jr[i], "touching interval");
CPP,
                    ],
                    [
                        'case_id' => 'h2_scale', 'name' => '1000 disjoint intervals', 'group' => 'scale',
                        'points' => 30, 'hidden' => true,
                        'body' => <<<'CPP'
    std::vector<std::vector<int>> a, b;
    for (int i = 0; i < 1000; ++i) { a.push_back({i * 2, i * 2 + 1}); b.push_back({i * 2, i * 2 + 1}); }
    ASSERT_EQ(u.merge(a).size(), j.merge(b).size(), "disjoint scale");
CPP,
                    ],
                ],
            ],

            'lru-cache' => [
                'reference' => <<<'CPP'
#pragma once
#include <list>
#include <unordered_map>
#include <utility>

namespace judge {
class LRUCache {
public:
    explicit LRUCache(int capacity) : capacity_(capacity) {}

    int get(int key) {
        auto it = index_.find(key);
        if (it == index_.end()) return -1;
        order_.splice(order_.begin(), order_, it->second);
        return it->second->second;
    }

    void put(int key, int value) {
        auto it = index_.find(key);
        if (it != index_.end()) {
            it->second->second = value;
            order_.splice(order_.begin(), order_, it->second);
            return;
        }
        if (static_cast<int>(order_.size()) == capacity_) {
            index_.erase(order_.back().first);
            order_.pop_back();
        }
        order_.emplace_front(key, value);
        index_[key] = order_.begin();
    }

private:
    int capacity_;
    std::list<std::pair<int, int>> order_;
    std::unordered_map<int, std::list<std::pair<int, int>>::iterator> index_;
};
}
CPP,
                'cases' => [
                    [
                        'case_id' => 'p1_evict', 'name' => 'Evict least recently used', 'group' => 'basic',
                        'points' => 20, 'hidden' => false,
                        'body' => <<<'CPP'
    UserLRUCache u(2); judge::LRUCache j(2);
    u.put(1, 1); j.put(1, 1); u.put(2, 2); j.put(2, 2);
    ASSERT_EQ(u.get(1), j.get(1), "get 1");
    u.put(3, 3); j.put(3, 3);
    ASSERT_EQ(u.get(2), j.get(2), "evicted key 2");
    ASSERT_EQ(u.get(3), j.get(3), "get 3");
CPP,
                    ],
                    [
                        'case_id' => 'p2_capacity_one', 'name' => 'Capacity of one', 'group' => 'edge',
                        'points' => 25, 'hidden' => false,
                        'body' => <<<'CPP'
    UserLRUCache u(1); judge::LRUCache j(1);
    u.put(1, 10); j.put(1, 10);
    u.put(2, 20); j.put(2, 20);
    ASSERT_EQ(u.get(1), j.get(1), "key 1 evicted");
    ASSERT_EQ(u.get(2), j.get(2), "key 2 present");
CPP,
                    ],
                    [
                        'case_id' => 'h1_update', 'name' => 'Update existing key', 'group' => 'edge',
                        'points' => 25, 'hidden' => true,
                        'body' => <<<'CPP'
    UserLRUCache u(2); judge::LRUCache j(2);
    u.put(1, 1); j.put(1, 1);
    u.put(1, 5); j.put(1, 5);
    ASSERT_EQ(u.get(1), j.get(1), "updated value");
CPP,
                    ],
                    [
                        'case_id' => 'h2_scale', 'name' => '500 operation sequence', 'group' => 'scale',
                        'points' => 30, 'hidden' => true,
                        'body' => <<<'CPP'
    UserLRUCache u(32); judge::LRUCache j(32);
    for (int i = 0; i < 500; ++i) {
        int key = i % 64;
        if (i % 3 == 0) {
            u.put(key, i); j.put(key, i);
        } else {
            ASSERT_EQ(u.get(key), j.get(key), "sequence get");
        }
    }
CPP,
                    ],
                ],
            ],

            'course-schedule' => [
                'reference' => <<<'CPP'
#pragma once
#include <vector>

namespace judge {
class Solution {
public:
    bool canFinish(int numCourses, std::vector<std::vector<int>>& prerequisites) {
        std::vector<std::vector<int>> graph(numCourses);
        std::vector<int> state(numCourses, 0);
        for (auto& edge : prerequisites) graph[edge[1]].push_back(edge[0]);
        for (int i = 0; i < numCourses; ++i) {
            if (state[i] == 0 && !visit(graph, state, i)) return false;
        }
        return true;
    }

private:
    bool visit(std::vector<std::vector<int>>& graph, std::vector<int>& state, int node) {
        if (state[node] == 1) return false;
        if (state[node] == 2) return true;
        state[node] = 1;
        for (int next : graph[node]) {
            if (!visit(graph, state, next)) return false;
        }
        state[node] = 2;
        return true;
    }
};
}
CPP,
                'cases' => [
                    [
                        'case_id' => 'p1_simple', 'name' => 'Single prerequisite', 'group' => 'basic',
                        'points' => 20, 'hidden' => false,
                        'body' => <<<'CPP'
    std::vector<std::vector<int>> a{{1, 0}}, b{{1, 0}};
    ASSERT_EQ(u.canFinish(2, a), j.canFinish(2, b), "simple edge");
CPP,
                    ],
                    [
                        'case_id' => 'p2_cycle', 'name' => 'Two course cycle', 'group' => 'cycle',
                        'points' => 25, 'hidden' => false,
                        'body' => <<<'CPP'
    std::vector<std::vector<int>> a{{1, 0}, {0, 1}}, b{{1, 0}, {0, 1}};
    ASSERT_EQ(u.canFinish(2, a), j.canFinish(2, b), "two course cycle");
CPP,
                    ],
                    [
                        'case_id' => 'h1_chain', 'name' => 'Linear chain', 'group' => 'edge',
                        'points' => 25, 'hidden' => true,
                        'body' => <<<'CPP'
    std::vector<std::vector<int>> a{{1, 0}, {2, 1}, {3, 2}}, b = a;
    ASSERT_EQ(u.canFinish(4, a), j.canFinish(4, b), "linear chain");
CPP,
                    ],
                    [
                        'case_id' => 'h2_scale', 'name' => '1000 course DAG', 'group' => 'scale',
                        'points' => 30, 'hidden' => true,
                        'body' => <<<'CPP'
    std::vector<std::vector<int>> a, b;
    for (int i = 1; i < 1000; ++i) { a.push_back({i, i - 1}); b.push_back({i, i - 1}); }
    ASSERT_EQ(u.canFinish(1000, a), j.canFinish(1000, b), "1000 course DAG");
CPP,
                    ],
                ],
            ],

            'trapping-rain-water' => [
                'reference' => <<<'CPP'
#pragma once
#include <algorithm>
#include <vector>

namespace judge {
class Solution {
public:
    long long trap(std::vector<int>& height) {
        long long total = 0;
        std::size_t left = 0, right = height.size();
        if (right < 3) return 0;
        --right;
        int leftMax = height[left], rightMax = height[right];
        while (left < right) {
            if (leftMax < rightMax) {
                total += leftMax - height[++left];
                leftMax = std::max(leftMax, height[left]);
            } else {
                total += rightMax - height[--right];
                rightMax = std::max(rightMax, height[right]);
            }
        }
        return total;
    }
};
}
CPP,
                'cases' => [
                    [
                        'case_id' => 'p1_classic', 'name' => 'Classic histogram', 'group' => 'basic',
                        'points' => 20, 'hidden' => false,
                        'body' => <<<'CPP'
    std::vector<int> a{0, 1, 0, 2, 1, 0, 1, 3, 2, 1, 2, 1}, b = a;
    ASSERT_EQ(u.trap(a), j.trap(b), "classic histogram");
CPP,
                    ],
                    [
                        'case_id' => 'p2_empty', 'name' => 'Empty terrain', 'group' => 'edge',
                        'points' => 25, 'hidden' => false,
                        'body' => <<<'CPP'
    std::vector<int> a, b;
    ASSERT_EQ(u.trap(a), j.trap(b), "empty terrain");
CPP,
                    ],
                    [
                        'case_id' => 'h1_two_bars', 'name' => 'Two boundary bars', 'group' => 'edge',
                        'points' => 25, 'hidden' => true,
                        'body' => <<<'CPP'
    std::vector<int> a{5, 0, 5}, b = a;
    ASSERT_EQ(u.trap(a), j.trap(b), "two boundary bars");
CPP,
                    ],
                    [
                        'case_id' => 'h2_scale', 'name' => 'Sawtooth scale', 'group' => 'scale',
                        'points' => 30, 'hidden' => true,
                        'body' => <<<'CPP'
    std::vector<int> a, b;
    for (int i = 0; i < 100000; ++i) {
        int value = (i % 2 == 0) ? 100 : 0;
        a.push_back(value); b.push_back(value);
    }
    ASSERT_EQ(u.trap(a), j.trap(b), "sawtooth scale");
CPP,
                    ],
                ],
            ],

            'median-of-two-sorted-arrays' => [
                'reference' => <<<'CPP'
#pragma once
#include <vector>

namespace judge {
class Solution {
public:
    double findMedianSortedArrays(std::vector<int>& nums1, std::vector<int>& nums2) {
        std::vector<int> merged;
        merged.reserve(nums1.size() + nums2.size());
        std::size_t i = 0, j = 0;
        while (i < nums1.size() && j < nums2.size()) {
            if (nums1[i] <= nums2[j]) merged.push_back(nums1[i++]);
            else merged.push_back(nums2[j++]);
        }
        while (i < nums1.size()) merged.push_back(nums1[i++]);
        while (j < nums2.size()) merged.push_back(nums2[j++]);
        std::size_t n = merged.size();
        if (n == 0) return 0.0;
        if (n % 2 == 1) return merged[n / 2];
        return (merged[n / 2 - 1] + merged[n / 2]) / 2.0;
    }
};
}
CPP,
                'cases' => [
                    [
                        'case_id' => 'p1_odd', 'name' => 'Odd combined length', 'group' => 'basic',
                        'points' => 20, 'hidden' => false,
                        'body' => <<<'CPP'
    std::vector<int> a{1, 3}, b{2}, c{1, 3}, d{2};
    ASSERT_NEAR(u.findMedianSortedArrays(a, b), j.findMedianSortedArrays(c, d), 1e-9, "odd median");
CPP,
                    ],
                    [
                        'case_id' => 'p2_even', 'name' => 'Even combined length', 'group' => 'basic',
                        'points' => 25, 'hidden' => false,
                        'body' => <<<'CPP'
    std::vector<int> a{1, 2}, b{3, 4}, c{1, 2}, d{3, 4};
    ASSERT_NEAR(u.findMedianSortedArrays(a, b), j.findMedianSortedArrays(c, d), 1e-9, "even median");
CPP,
                    ],
                    [
                        'case_id' => 'h1_empty', 'name' => 'One empty array', 'group' => 'edge',
                        'points' => 25, 'hidden' => true,
                        'body' => <<<'CPP'
    std::vector<int> a, b{7}, c, d{7};
    ASSERT_NEAR(u.findMedianSortedArrays(a, b), j.findMedianSortedArrays(c, d), 1e-9, "empty side");
CPP,
                    ],
                    [
                        'case_id' => 'h2_scale', 'name' => 'Large sorted arrays', 'group' => 'scale',
                        'points' => 30, 'hidden' => true,
                        'body' => <<<'CPP'
    std::vector<int> a, b, c, d;
    for (int i = 0; i < 50000; ++i) { a.push_back(i * 2); c.push_back(i * 2); }
    for (int i = 0; i < 50000; ++i) { b.push_back(i * 2 + 1); d.push_back(i * 2 + 1); }
    ASSERT_NEAR(u.findMedianSortedArrays(a, b), j.findMedianSortedArrays(c, d), 1e-9, "large median");
CPP,
                    ],
                ],
            ],

            'word-ladder' => [
                'reference' => <<<'CPP'
#pragma once
#include <queue>
#include <string>
#include <unordered_set>
#include <vector>

namespace judge {
class Solution {
public:
    int ladderLength(std::string beginWord, std::string endWord, std::vector<std::string>& wordList) {
        std::unordered_set<std::string> words(wordList.begin(), wordList.end());
        if (!words.count(endWord)) return 0;
        std::queue<std::pair<std::string, int>> frontier;
        frontier.push({beginWord, 1});
        while (!frontier.empty()) {
            auto [word, depth] = frontier.front();
            frontier.pop();
            if (word == endWord) return depth;
            for (std::size_t i = 0; i < word.size(); ++i) {
                char original = word[i];
                for (char c = 'a'; c <= 'z'; ++c) {
                    word[i] = c;
                    auto it = words.find(word);
                    if (it != words.end()) {
                        words.erase(it);
                        frontier.push({word, depth + 1});
                    }
                }
                word[i] = original;
            }
        }
        return 0;
    }
};
}
CPP,
                'cases' => [
                    [
                        'case_id' => 'p1_classic', 'name' => 'Classic word ladder', 'group' => 'basic',
                        'points' => 20, 'hidden' => false,
                        'body' => <<<'CPP'
    std::vector<std::string> a{"hot", "dot", "dog", "lot", "log", "cog"}, b = a;
    ASSERT_EQ(u.ladderLength("hit", "cog", a), j.ladderLength("hit", "cog", b), "classic ladder");
CPP,
                    ],
                    [
                        'case_id' => 'p2_unreachable', 'name' => 'Unreachable end word', 'group' => 'basic',
                        'points' => 25, 'hidden' => false,
                        'body' => <<<'CPP'
    std::vector<std::string> a{"hot", "dot", "dog"}, b = a;
    ASSERT_EQ(u.ladderLength("hit", "cog", a), j.ladderLength("hit", "cog", b), "unreachable end");
CPP,
                    ],
                    [
                        'case_id' => 'h1_missing', 'name' => 'End word absent from list', 'group' => 'edge',
                        'points' => 25, 'hidden' => true,
                        'body' => <<<'CPP'
    std::vector<std::string> a{"hot", "dot"}, b = a;
    ASSERT_EQ(u.ladderLength("hit", "cog", a), j.ladderLength("hit", "cog", b), "missing end word");
CPP,
                    ],
                    [
                        'case_id' => 'h2_scale', 'name' => 'Long chain scale', 'group' => 'scale',
                        'points' => 30, 'hidden' => true,
                        'body' => <<<'CPP'
    std::vector<std::string> a, b;
    for (int i = 0; i < 500; ++i) {
        std::string word = "word";
        word[0] = static_cast<char>('a' + (i % 26));
        word[1] = static_cast<char>('a' + (i / 26) % 26);
        word[2] = static_cast<char>('a' + (i / 676) % 26);
        a.push_back(word); b.push_back(word);
    }
    ASSERT_EQ(u.ladderLength("word", "word", a), j.ladderLength("word", "word", b), "long chain scale");
CPP,
                    ],
                ],
            ],

            'ring-buffer' => [
                'reference' => <<<'CPP'
#pragma once
#include <atomic>
#include <cstddef>
#include <vector>

namespace arena {
template <typename T>
class RingBuffer {
public:
    explicit RingBuffer(std::size_t capacity) : storage_(capacity), capacity_(capacity) {}

    bool push(const T& value) {
        if (size() >= capacity_) return false;
        storage_[write_.load(std::memory_order_relaxed) % capacity_] = value;
        write_.fetch_add(1, std::memory_order_release);
        return true;
    }

    bool pop(T& out) {
        if (size() == 0) return false;
        out = storage_[read_.load(std::memory_order_relaxed) % capacity_];
        read_.fetch_add(1, std::memory_order_release);
        return true;
    }

    std::size_t size() const noexcept {
        return write_.load(std::memory_order_acquire) - read_.load(std::memory_order_acquire);
    }

private:
    std::vector<T> storage_;
    std::size_t capacity_;
    std::atomic<std::size_t> write_{0};
    std::atomic<std::size_t> read_{0};
};
}
CPP,
                'cases' => [
                    [
                        'case_id' => 'p1_fifo', 'name' => 'FIFO ordering', 'group' => 'basic',
                        'points' => 20, 'hidden' => false,
                        'body' => <<<'CPP'
    user_arena::RingBuffer<int> u(4); arena::RingBuffer<int> j(4);
    ASSERT_EQ(u.push(1), j.push(1), "push 1");
    ASSERT_EQ(u.push(2), j.push(2), "push 2");
    int a = 0, b = 0;
    ASSERT_EQ(u.pop(a), j.pop(b), "pop");
    ASSERT_EQ(a, b, "FIFO value");
CPP,
                    ],
                    [
                        'case_id' => 'p2_capacity', 'name' => 'Capacity rejection', 'group' => 'edge',
                        'points' => 25, 'hidden' => false,
                        'body' => <<<'CPP'
    user_arena::RingBuffer<int> u(2); arena::RingBuffer<int> j(2);
    u.push(1); j.push(1); u.push(2); j.push(2);
    ASSERT_EQ(u.push(3), j.push(3), "reject over capacity");
    ASSERT_EQ(u.size(), j.size(), "size at capacity");
CPP,
                    ],
                    [
                        'case_id' => 'h1_wrap', 'name' => 'Wrap-around reuse', 'group' => 'edge',
                        'points' => 25, 'hidden' => true,
                        'body' => <<<'CPP'
    user_arena::RingBuffer<int> u(3); arena::RingBuffer<int> j(3);
    for (int i = 1; i <= 3; ++i) { u.push(i); j.push(i); }
    int a = 0, b = 0;
    u.pop(a); j.pop(b);
    u.push(4); j.push(4);
    for (int i = 0; i < 3; ++i) {
        ASSERT_EQ(u.pop(a), j.pop(b), "wrap pop");
        ASSERT_EQ(a, b, "wrap value");
    }
CPP,
                    ],
                    [
                        'case_id' => 'h2_scale', 'name' => '10k push/pop scale', 'group' => 'scale',
                        'points' => 30, 'hidden' => true,
                        'body' => <<<'CPP'
    user_arena::RingBuffer<int> u(128); arena::RingBuffer<int> j(128);
    for (int i = 0; i < 10000; ++i) {
        ASSERT_EQ(u.push(i), j.push(i), "scale push");
        if (i % 2 == 0) {
            int a = 0, b = 0;
            ASSERT_EQ(u.pop(a), j.pop(b), "scale pop");
            ASSERT_EQ(a, b, "scale value");
        }
    }
    ASSERT_EQ(u.size(), j.size(), "scale size");
CPP,
                    ],
                ],
            ],

            'lock-free-stack' => [
                'reference' => <<<'CPP'
#pragma once
#include <atomic>

namespace arena {
struct HazardPointers {};

template <typename T>
class LockFreeStack {
    struct Node {
        T value;
        Node* next;
    };

public:
    explicit LockFreeStack(HazardPointers&) {}

    void push(T value) {
        Node* node = new Node{std::move(value), head_.load(std::memory_order_relaxed)};
        while (!head_.compare_exchange_weak(node->next, node, std::memory_order_release, std::memory_order_relaxed)) {
        }
    }

    bool pop(T& out) {
        Node* head = head_.load(std::memory_order_acquire);
        while (head != nullptr) {
            if (head_.compare_exchange_weak(head, head->next, std::memory_order_acquire, std::memory_order_relaxed)) {
                out = head->value;
                delete head;
                return true;
            }
        }
        return false;
    }

    bool empty() const noexcept {
        return head_.load(std::memory_order_acquire) == nullptr;
    }

private:
    std::atomic<Node*> head_{nullptr};
};
}
CPP,
                'cases' => [
                    [
                        'case_id' => 'p1_lifo', 'name' => 'LIFO ordering', 'group' => 'basic',
                        'points' => 20, 'hidden' => false,
                        'extra' => 'namespace user_arena { struct HazardPointers {}; }',
                        'body' => <<<'CPP'
    arena::HazardPointers jhp; user_arena::HazardPointers uhp;
    arena::LockFreeStack<int> j(jhp); user_arena::LockFreeStack<int> u(uhp);
    u.push(1); j.push(1); u.push(2); j.push(2);
    int a = 0, b = 0;
    ASSERT_EQ(u.pop(a), j.pop(b), "pop");
    ASSERT_EQ(a, b, "LIFO value");
CPP,
                    ],
                    [
                        'case_id' => 'p2_empty', 'name' => 'Pop on empty stack', 'group' => 'edge',
                        'points' => 25, 'hidden' => false,
                        'extra' => 'namespace user_arena { struct HazardPointers {}; }',
                        'body' => <<<'CPP'
    arena::HazardPointers jhp; user_arena::HazardPointers uhp;
    arena::LockFreeStack<int> j(jhp); user_arena::LockFreeStack<int> u(uhp);
    int a = 0, b = 0;
    ASSERT_EQ(u.pop(a), j.pop(b), "empty pop");
    ASSERT_EQ(u.empty(), j.empty(), "empty state");
CPP,
                    ],
                    [
                        'case_id' => 'h1_duplicates', 'name' => 'Duplicate values', 'group' => 'edge',
                        'points' => 25, 'hidden' => true,
                        'extra' => 'namespace user_arena { struct HazardPointers {}; }',
                        'body' => <<<'CPP'
    arena::HazardPointers jhp; user_arena::HazardPointers uhp;
    arena::LockFreeStack<int> j(jhp); user_arena::LockFreeStack<int> u(uhp);
    for (int i = 0; i < 5; ++i) { u.push(7); j.push(7); }
    for (int i = 0; i < 5; ++i) {
        int a = 0, b = 0;
        ASSERT_EQ(u.pop(a), j.pop(b), "duplicate pop");
        ASSERT_EQ(a, b, "duplicate value");
    }
CPP,
                    ],
                    [
                        'case_id' => 'h2_scale', 'name' => '100 node scale', 'group' => 'scale',
                        'points' => 30, 'hidden' => true,
                        'extra' => 'namespace user_arena { struct HazardPointers {}; }',
                        'body' => <<<'CPP'
    arena::HazardPointers jhp; user_arena::HazardPointers uhp;
    arena::LockFreeStack<int> j(jhp); user_arena::LockFreeStack<int> u(uhp);
    for (int i = 0; i < 100; ++i) { u.push(i); j.push(i); }
    for (int i = 99; i >= 0; --i) {
        int a = 0, b = 0;
        ASSERT_EQ(u.pop(a), j.pop(b), "scale pop");
        ASSERT_EQ(a, b, "scale LIFO value");
    }
    ASSERT_EQ(u.empty(), j.empty(), "scale empty");
CPP,
                    ],
                ],
            ],

            'dangling-reference' => [
                'reference' => <<<'CPP'
#pragma once
#include <filesystem>
#include <fstream>
#include <string>
#include <string_view>
#include <unordered_map>

namespace arena {
struct Config {
    std::unordered_map<std::string, std::string_view> values;
    std::string backing;
};

inline Config load_config(const std::filesystem::path& path) {
    Config config;
    std::ifstream input(path);
    std::string line;
    while (std::getline(input, line)) {
        config.backing += line;
        config.backing += '\n';
    }
    std::size_t start = 0;
    while (start < config.backing.size()) {
        std::size_t end = config.backing.find('\n', start);
        if (end == std::string::npos) break;
        std::string_view entry(config.backing.data() + start, end - start);
        std::size_t equals = entry.find('=');
        if (equals != std::string_view::npos) {
            config.values.emplace(std::string(entry.substr(0, equals)), entry.substr(equals + 1));
        }
        start = end + 1;
    }
    return config;
}
}
CPP,
                'cases' => [
                    [
                        'case_id' => 'p1_basic', 'name' => 'Two key config', 'group' => 'basic',
                        'points' => 20, 'hidden' => false,
                        'extra' => '#include <fstream>',
                        'body' => <<<'CPP'
    std::ofstream("cfg_basic.cfg") << "host=localhost\nport=8080\n";
    auto ur = user_arena::load_config("cfg_basic.cfg");
    auto jr = arena::load_config("cfg_basic.cfg");
    ASSERT_EQ(std::string(ur.values.at("host")), std::string(jr.values.at("host")), "host value");
    ASSERT_EQ(std::string(ur.values.at("port")), std::string(jr.values.at("port")), "port value");
CPP,
                    ],
                    [
                        'case_id' => 'p2_missing', 'name' => 'Missing file', 'group' => 'edge',
                        'points' => 25, 'hidden' => false,
                        'body' => <<<'CPP'
    auto ur = user_arena::load_config("does_not_exist.cfg");
    auto jr = arena::load_config("does_not_exist.cfg");
    ASSERT_EQ(ur.values.size(), jr.values.size(), "missing file size");
CPP,
                    ],
                    [
                        'case_id' => 'h1_empty', 'name' => 'Empty config', 'group' => 'edge',
                        'points' => 25, 'hidden' => true,
                        'extra' => '#include <fstream>',
                        'body' => <<<'CPP'
    std::ofstream("cfg_empty.cfg") << "";
    auto ur = user_arena::load_config("cfg_empty.cfg");
    auto jr = arena::load_config("cfg_empty.cfg");
    ASSERT_EQ(ur.values.size(), jr.values.size(), "empty config size");
CPP,
                    ],
                    [
                        'case_id' => 'h2_scale', 'name' => 'Many key scale', 'group' => 'scale',
                        'points' => 30, 'hidden' => true,
                        'extra' => '#include <fstream>',
                        'body' => <<<'CPP'
    std::ofstream out("cfg_scale.cfg");
    for (int i = 0; i < 500; ++i) out << "key" << i << "=value" << i << "\n";
    out.close();
    auto ur = user_arena::load_config("cfg_scale.cfg");
    auto jr = arena::load_config("cfg_scale.cfg");
    ASSERT_EQ(ur.values.size(), jr.values.size(), "scale key count");
    ASSERT_EQ(std::string(ur.values.at("key499")), std::string(jr.values.at("key499")), "scale last key");
CPP,
                    ],
                ],
            ],

            'hot-loop-optimization' => [
                'reference' => <<<'CPP'
#pragma once
#include <cstddef>
#include <cstdint>

namespace arena {
inline void histogram(const std::uint8_t* data, std::size_t size, std::uint64_t bins[256]) {
    std::uint64_t local[256] = {};
    std::size_t i = 0;
    for (; i + 4 <= size; i += 4) {
        ++local[data[i]];
        ++local[data[i + 1]];
        ++local[data[i + 2]];
        ++local[data[i + 3]];
    }
    for (; i < size; ++i) ++local[data[i]];
    for (int b = 0; b < 256; ++b) bins[b] = local[b];
}
}
CPP,
                'cases' => [
                    [
                        'case_id' => 'p1_empty', 'name' => 'Empty buffer', 'group' => 'edge',
                        'points' => 20, 'hidden' => false,
                        'body' => <<<'CPP'
    std::uint64_t ua[256] = {}, ja[256] = {};
    user_arena::histogram(nullptr, 0, ua); arena::histogram(nullptr, 0, ja);
    for (int i = 0; i < 256; ++i) ASSERT_EQ(ua[i], ja[i], "empty bin");
CPP,
                    ],
                    [
                        'case_id' => 'p2_all_bytes', 'name' => 'Every byte value', 'group' => 'basic',
                        'points' => 25, 'hidden' => false,
                        'body' => <<<'CPP'
    std::vector<std::uint8_t> data;
    for (int i = 0; i < 256; ++i) data.push_back(static_cast<std::uint8_t>(i));
    std::uint64_t ua[256] = {}, ja[256] = {};
    user_arena::histogram(data.data(), data.size(), ua); arena::histogram(data.data(), data.size(), ja);
    for (int i = 0; i < 256; ++i) ASSERT_EQ(ua[i], ja[i], "all byte bin");
CPP,
                    ],
                    [
                        'case_id' => 'h1_pattern', 'name' => 'Repeated pattern', 'group' => 'edge',
                        'points' => 25, 'hidden' => true,
                        'body' => <<<'CPP'
    std::vector<std::uint8_t> data;
    for (int i = 0; i < 10000; ++i) data.push_back(static_cast<std::uint8_t>(i % 7));
    std::uint64_t ua[256] = {}, ja[256] = {};
    user_arena::histogram(data.data(), data.size(), ua); arena::histogram(data.data(), data.size(), ja);
    for (int i = 0; i < 256; ++i) ASSERT_EQ(ua[i], ja[i], "pattern bin");
CPP,
                    ],
                    [
                        'case_id' => 'h2_scale', 'name' => '1 MB scale', 'group' => 'scale',
                        'points' => 30, 'hidden' => true,
                        'body' => <<<'CPP'
    std::vector<std::uint8_t> data(1u << 20u);
    for (std::size_t i = 0; i < data.size(); ++i) data[i] = static_cast<std::uint8_t>((i * 31u) & 0xffu);
    std::uint64_t ua[256] = {}, ja[256] = {};
    user_arena::histogram(data.data(), data.size(), ua); arena::histogram(data.data(), data.size(), ja);
    for (int i = 0; i < 256; ++i) ASSERT_EQ(ua[i], ja[i], "scale bin");
CPP,
                    ],
                ],
            ],

            'concept-constrained-container' => [
                'reference' => <<<'CPP'
#pragma once
#include <concepts>
#include <cstddef>
#include <new>
#include <utility>

namespace arena {
template <typename T, std::size_t N>
    requires std::movable<T> && std::destructible<T>
class InlineVector {
public:
    InlineVector() noexcept = default;

    bool push_back(const T& value) {
        if (size_ == N) return false;
        ::new (static_cast<void*>(storage_ + size_ * sizeof(T))) T(value);
        ++size_;
        return true;
    }

    bool push_back(T&& value) {
        if (size_ == N) return false;
        ::new (static_cast<void*>(storage_ + size_ * sizeof(T))) T(std::move(value));
        ++size_;
        return true;
    }

    std::size_t size() const noexcept { return size_; }

private:
    alignas(T) unsigned char storage_[sizeof(T) * N];
    std::size_t size_ = 0;
};
}
CPP,
                'cases' => [
                    [
                        'case_id' => 'p1_push', 'name' => 'Push and size', 'group' => 'basic',
                        'points' => 20, 'hidden' => false,
                        'body' => <<<'CPP'
    user_arena::InlineVector<int, 4> u; arena::InlineVector<int, 4> j;
    ASSERT_EQ(u.push_back(1), j.push_back(1), "push 1");
    ASSERT_EQ(u.push_back(2), j.push_back(2), "push 2");
    ASSERT_EQ(u.size(), j.size(), "size after push");
CPP,
                    ],
                    [
                        'case_id' => 'p2_move', 'name' => 'Move-only values', 'group' => 'basic',
                        'points' => 25, 'hidden' => false,
                        'body' => <<<'CPP'
    user_arena::InlineVector<std::string, 2> u; arena::InlineVector<std::string, 2> j;
    ASSERT_EQ(u.push_back(std::string("alpha")), j.push_back(std::string("alpha")), "move push");
    ASSERT_EQ(u.size(), j.size(), "move size");
CPP,
                    ],
                    [
                        'case_id' => 'h1_overflow', 'name' => 'Capacity overflow', 'group' => 'edge',
                        'points' => 25, 'hidden' => true,
                        'body' => <<<'CPP'
    user_arena::InlineVector<int, 2> u; arena::InlineVector<int, 2> j;
    u.push_back(1); j.push_back(1); u.push_back(2); j.push_back(2);
    ASSERT_EQ(u.push_back(3), j.push_back(3), "overflow rejected");
    ASSERT_EQ(u.size(), j.size(), "overflow size");
CPP,
                    ],
                    [
                        'case_id' => 'h2_scale', 'name' => 'Full inline capacity', 'group' => 'scale',
                        'points' => 30, 'hidden' => true,
                        'body' => <<<'CPP'
    user_arena::InlineVector<int, 64> u; arena::InlineVector<int, 64> j;
    for (int i = 0; i < 64; ++i) {
        ASSERT_EQ(u.push_back(i), j.push_back(i), "scale push");
    }
    ASSERT_EQ(u.size(), j.size(), "scale size");
CPP,
                    ],
                ],
            ],

            'span-overflow' => [
                'reference' => <<<'CPP'
#pragma once
#include <cstddef>
#include <cstdint>
#include <cstring>
#include <span>

namespace arena {
struct Header {
    std::uint16_t payload_length;
};

inline bool decode(std::span<const std::byte> packet, std::span<std::byte> out) {
    if (packet.size() < sizeof(Header)) return false;
    Header header;
    std::memcpy(&header, packet.data(), sizeof(header));
    if (packet.size() < sizeof(Header) + header.payload_length) return false;
    if (out.size() < header.payload_length) return false;
    std::memcpy(out.data(), packet.data() + sizeof(Header), header.payload_length);
    return true;
}
}
CPP,
                'cases' => [
                    [
                        'case_id' => 'p1_valid', 'name' => 'Valid packet', 'group' => 'basic',
                        'points' => 20, 'hidden' => false,
                        'body' => <<<'CPP'
    std::vector<std::byte> packet(sizeof(std::uint16_t) + 3);
    std::uint16_t length = 3;
    std::memcpy(packet.data(), &length, sizeof(length));
    std::memcpy(packet.data() + sizeof(length), "abc", 3);
    std::vector<std::byte> out(8);
    ASSERT_EQ(user_arena::decode(packet, out), arena::decode(packet, out), "valid packet");
CPP,
                    ],
                    [
                        'case_id' => 'p2_short', 'name' => 'Truncated header', 'group' => 'edge',
                        'points' => 25, 'hidden' => false,
                        'body' => <<<'CPP'
    std::vector<std::byte> packet(1);
    std::vector<std::byte> out(8);
    ASSERT_EQ(user_arena::decode(packet, out), arena::decode(packet, out), "truncated header");
CPP,
                    ],
                    [
                        'case_id' => 'h1_overrun', 'name' => 'Payload overruns packet', 'group' => 'edge',
                        'points' => 25, 'hidden' => true,
                        'body' => <<<'CPP'
    std::vector<std::byte> packet(sizeof(std::uint16_t) + 1);
    std::uint16_t length = 100;
    std::memcpy(packet.data(), &length, sizeof(length));
    std::vector<std::byte> out(8);
    ASSERT_EQ(user_arena::decode(packet, out), arena::decode(packet, out), "payload overrun");
CPP,
                    ],
                    [
                        'case_id' => 'h2_small_out', 'name' => 'Output buffer too small', 'group' => 'edge',
                        'points' => 30, 'hidden' => true,
                        'body' => <<<'CPP'
    std::vector<std::byte> packet(sizeof(std::uint16_t) + 4);
    std::uint16_t length = 4;
    std::memcpy(packet.data(), &length, sizeof(length));
    std::memcpy(packet.data() + sizeof(length), "data", 4);
    std::vector<std::byte> out(2);
    ASSERT_EQ(user_arena::decode(packet, out), arena::decode(packet, out), "small output");
CPP,
                    ],
                ],
            ],

            'small-string-optimization' => [
                'reference' => <<<'CPP'
#pragma once
#include <cstddef>
#include <string>
#include <string_view>

namespace arena {
class SmallString {
public:
    SmallString() = default;
    explicit SmallString(std::string_view value) : text_(value) {}
    std::string_view view() const noexcept { return text_; }

private:
    std::string text_;
};
}
CPP,
                'cases' => [
                    [
                        'case_id' => 'p1_empty', 'name' => 'Empty string', 'group' => 'edge',
                        'points' => 20, 'hidden' => false,
                        'body' => <<<'CPP'
    user_arena::SmallString u; arena::SmallString j;
    ASSERT_EQ(std::string(u.view()), std::string(j.view()), "empty view");
CPP,
                    ],
                    [
                        'case_id' => 'p2_short', 'name' => 'Short inline value', 'group' => 'basic',
                        'points' => 25, 'hidden' => false,
                        'body' => <<<'CPP'
    user_arena::SmallString u("arena"); arena::SmallString j("arena");
    ASSERT_EQ(std::string(u.view()), std::string(j.view()), "short view");
CPP,
                    ],
                    [
                        'case_id' => 'h1_long', 'name' => 'Long heap value', 'group' => 'edge',
                        'points' => 25, 'hidden' => true,
                        'body' => <<<'CPP'
    std::string value(128, 'x');
    user_arena::SmallString u(value); arena::SmallString j(value);
    ASSERT_EQ(std::string(u.view()), std::string(j.view()), "long view");
CPP,
                    ],
                    [
                        'case_id' => 'h2_move', 'name' => 'Move construction', 'group' => 'basic',
                        'points' => 30, 'hidden' => true,
                        'body' => <<<'CPP'
    user_arena::SmallString u("move me"); arena::SmallString j("move me");
    user_arena::SmallString um(std::move(u)); arena::SmallString jm(std::move(j));
    ASSERT_EQ(std::string(um.view()), std::string(jm.view()), "moved view");
CPP,
                    ],
                ],
            ],

            'move-semantics-audit' => [
                'reference' => <<<'CPP'
#pragma once

namespace arena {
struct MoveAudit {
    static constexpr bool is_move_just_a_cast = true;
    static constexpr bool moved_from_is_valid_unspecified = true;
};
}
CPP,
                'cases' => [
                    [
                        'case_id' => 'p1_cast', 'name' => 'Move is a cast', 'group' => 'basic',
                        'points' => 20, 'hidden' => false,
                        'body' => '    static_assert(user_arena::MoveAudit::is_move_just_a_cast == arena::MoveAudit::is_move_just_a_cast, "move is a cast");',
                    ],
                    [
                        'case_id' => 'p2_unspecified', 'name' => 'Moved-from state', 'group' => 'basic',
                        'points' => 25, 'hidden' => false,
                        'body' => '    static_assert(user_arena::MoveAudit::moved_from_is_valid_unspecified == arena::MoveAudit::moved_from_is_valid_unspecified, "moved-from state");',
                    ],
                    [
                        'case_id' => 'h1_traits', 'name' => 'Trait agreement', 'group' => 'edge',
                        'points' => 25, 'hidden' => true,
                        'body' => '    static_assert(user_arena::MoveAudit::is_move_just_a_cast, "user trait enabled");',
                    ],
                    [
                        'case_id' => 'h2_reference', 'name' => 'Reference agreement', 'group' => 'edge',
                        'points' => 30, 'hidden' => true,
                        'body' => '    static_assert(arena::MoveAudit::moved_from_is_valid_unspecified, "reference trait enabled");',
                    ],
                ],
            ],

            'thread-pool' => [
                'reference' => <<<'CPP'
#pragma once
#include <condition_variable>
#include <cstddef>
#include <functional>
#include <future>
#include <memory>
#include <mutex>
#include <queue>
#include <thread>
#include <type_traits>
#include <utility>
#include <vector>

namespace arena {
class ThreadPool {
public:
    explicit ThreadPool(std::size_t workers) {
        for (std::size_t i = 0; i < workers; ++i) {
            workers_.emplace_back([this] {
                for (;;) {
                    std::function<void()> task;
                    {
                        std::unique_lock<std::mutex> lock(mutex_);
                        ready_.wait(lock, [this] { return stopping_ || !tasks_.empty(); });
                        if (stopping_ && tasks_.empty()) return;
                        task = std::move(tasks_.front());
                        tasks_.pop();
                    }
                    task();
                }
            });
        }
    }

    ~ThreadPool() {
        {
            std::lock_guard<std::mutex> lock(mutex_);
            stopping_ = true;
        }
        ready_.notify_all();
        for (auto& worker : workers_) worker.join();
    }

    template <typename F>
    auto submit(F&& task) -> std::future<std::invoke_result_t<F>> {
        using Result = std::invoke_result_t<F>;
        auto job = std::make_shared<std::packaged_task<Result()>>(std::forward<F>(task));
        auto future = job->get_future();
        {
            std::lock_guard<std::mutex> lock(mutex_);
            tasks_.emplace([job] { (*job)(); });
        }
        ready_.notify_one();
        return future;
    }

private:
    std::vector<std::thread> workers_;
    std::queue<std::function<void()>> tasks_;
    std::mutex mutex_;
    std::condition_variable ready_;
    bool stopping_ = false;
};
}
CPP,
                'cases' => [
                    [
                        'case_id' => 'p1_value', 'name' => 'Single task result', 'group' => 'basic',
                        'points' => 20, 'hidden' => false,
                        'body' => <<<'CPP'
    user_arena::ThreadPool u(2); arena::ThreadPool j(2);
    auto uf = u.submit([] { return 42; });
    auto jf = j.submit([] { return 42; });
    ASSERT_EQ(uf.get(), jf.get(), "task result");
CPP,
                    ],
                    [
                        'case_id' => 'p2_batch', 'name' => 'Batch of tasks', 'group' => 'basic',
                        'points' => 25, 'hidden' => false,
                        'body' => <<<'CPP'
    user_arena::ThreadPool u(4); arena::ThreadPool j(4);
    std::vector<std::future<int>> uf, jf;
    for (int i = 0; i < 16; ++i) {
        uf.push_back(u.submit([i] { return i * i; }));
        jf.push_back(j.submit([i] { return i * i; }));
    }
    for (int i = 0; i < 16; ++i) ASSERT_EQ(uf[i].get(), jf[i].get(), "batch result");
CPP,
                    ],
                    [
                        'case_id' => 'h1_exception', 'name' => 'Exception propagation', 'group' => 'edge',
                        'points' => 25, 'hidden' => true,
                        'extra' => '#include <stdexcept>',
                        'body' => <<<'CPP'
    user_arena::ThreadPool u(2); arena::ThreadPool j(2);
    bool ue = false, je = false;
    try { u.submit([] { throw std::runtime_error("boom"); }).get(); } catch (...) { ue = true; }
    try { j.submit([] { throw std::runtime_error("boom"); }).get(); } catch (...) { je = true; }
    ASSERT_EQ(ue, je, "exception propagation");
CPP,
                    ],
                    [
                        'case_id' => 'h2_scale', 'name' => '100 task scale', 'group' => 'scale',
                        'points' => 30, 'hidden' => true,
                        'body' => <<<'CPP'
    user_arena::ThreadPool u(4); arena::ThreadPool j(4);
    std::vector<std::future<int>> uf, jf;
    for (int i = 0; i < 100; ++i) {
        uf.push_back(u.submit([i] { return i + 1; }));
        jf.push_back(j.submit([i] { return i + 1; }));
    }
    for (int i = 0; i < 100; ++i) ASSERT_EQ(uf[i].get(), jf[i].get(), "scale result");
CPP,
                    ],
                ],
            ],

            'constexpr-matrix' => [
                'reference' => <<<'CPP'
#pragma once
#include <cstddef>

namespace arena {
template <typename T, std::size_t R, std::size_t C>
struct Matrix {
    T data[R][C]{};

    constexpr T& operator()(std::size_t r, std::size_t c) { return data[r][c]; }
    constexpr const T& operator()(std::size_t r, std::size_t c) const { return data[r][c]; }
};

template <typename T, std::size_t R, std::size_t K, std::size_t C>
constexpr Matrix<T, R, C> multiply(const Matrix<T, R, K>& lhs, const Matrix<T, K, C>& rhs) {
    Matrix<T, R, C> result{};
    for (std::size_t r = 0; r < R; ++r) {
        for (std::size_t c = 0; c < C; ++c) {
            T sum{};
            for (std::size_t k = 0; k < K; ++k) sum += lhs(r, k) * rhs(k, c);
            result(r, c) = sum;
        }
    }
    return result;
}
}
CPP,
                'cases' => [
                    [
                        'case_id' => 'p1_identity', 'name' => 'Identity product', 'group' => 'basic',
                        'points' => 20, 'hidden' => false,
                        'body' => <<<'CPP'
    arena::Matrix<int, 2, 2> i{};
    i(0, 0) = 1; i(1, 1) = 1;
    arena::Matrix<int, 2, 2> a{};
    a(0, 0) = 3; a(0, 1) = 4; a(1, 0) = 5; a(1, 1) = 6;
    auto product = arena::multiply(a, i);
    ASSERT_EQ(product(0, 0), 3, "identity 00");
    ASSERT_EQ(product(1, 1), 6, "identity 11");
CPP,
                    ],
                    [
                        'case_id' => 'p2_2x2', 'name' => '2x2 product', 'group' => 'basic',
                        'points' => 25, 'hidden' => false,
                        'body' => <<<'CPP'
    arena::Matrix<int, 2, 2> a{}, b{};
    a(0, 0) = 1; a(0, 1) = 2; a(1, 0) = 3; a(1, 1) = 4;
    b(0, 0) = 5; b(0, 1) = 6; b(1, 0) = 7; b(1, 1) = 8;
    auto p = arena::multiply(a, b);
    ASSERT_EQ(p(0, 0), 19, "product 00");
    ASSERT_EQ(p(0, 1), 22, "product 01");
    ASSERT_EQ(p(1, 0), 43, "product 10");
    ASSERT_EQ(p(1, 1), 50, "product 11");
CPP,
                    ],
                    [
                        'case_id' => 'h1_rect', 'name' => 'Rectangular product', 'group' => 'edge',
                        'points' => 25, 'hidden' => true,
                        'body' => <<<'CPP'
    arena::Matrix<int, 2, 3> a{};
    arena::Matrix<int, 3, 1> b{};
    for (int r = 0; r < 2; ++r) for (int c = 0; c < 3; ++c) a(r, c) = r + c + 1;
    b(0, 0) = 1; b(1, 0) = 2; b(2, 0) = 3;
    auto p = arena::multiply(a, b);
    ASSERT_EQ(p(0, 0), 14, "rect product 0");
    ASSERT_EQ(p(1, 0), 20, "rect product 1");
CPP,
                    ],
                    [
                        'case_id' => 'h2_constexpr', 'name' => 'Compile-time evaluation', 'group' => 'scale',
                        'points' => 30, 'hidden' => true,
                        'body' => <<<'CPP'
    constexpr arena::Matrix<int, 2, 2> a{};
    constexpr arena::Matrix<int, 2, 2> b{};
    constexpr auto p = arena::multiply(a, b);
    static_assert(p(0, 0) == 0 && p(1, 1) == 0, "constexpr product");
CPP,
                    ],
                ],
            ],
        ];
    }
}
