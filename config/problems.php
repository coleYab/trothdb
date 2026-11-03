<?php

return [
    [
        'id' => 'two-sum',
        'number' => 1,
        'title' => 'Two Sum',
        'difficulty' => 'Easy',
        'acceptance_rate' => 54.3,
        'tags' => ['Array', 'Hash Table'],
        'summary' => 'Find the two indices in an array whose values add up to a given target.',
        'description' => [
            'You are given an array of integers nums and an integer target. Your task is to find two distinct indices i and j in the array such that nums[i] + nums[j] equals target, and return them as a pair. The order of the two indices does not matter, and the problem guarantees that every input has exactly one valid answer.',
            'A straightforward solution is to examine every possible pair of indices. For each i, scan every j greater than i and check whether the two values add up to the target. This is correct but takes O(n^2) time, which becomes too slow once the array grows to 100,000 elements. During an interview, that solution is usually treated as a starting point rather than the final answer.',
            'The usual improvement is to trade memory for speed. As you walk through the array once from left to right, keep a hash map from each value you have already seen to the index where it appeared. At position i you only need to know whether target - nums[i] is already in the map. If it is, you have found both indices and can return immediately. Otherwise, store nums[i] mapped to i and continue. Each element is inserted and looked up at most once, so the whole pass runs in O(n) expected time.',
            'Pay close attention to the order of the lookup and the insertion. If you insert the current value before checking, an element could be paired with itself when target is exactly twice that value. Performing the lookup first makes the two returned indices guaranteed to be distinct. Also remember that the answer should contain the original indices, not sorted positions, so sorting the array first is a trap unless you keep track of where each value came from.',
            'The function should return an array or list with exactly two integers. If you are writing in a language with fixed-size arrays, make sure the returned container has room for both indices. The judge accepts the pair in either order, so [0, 1] and [1, 0] are both valid for the first example.',
        ],
        'examples' => [
            [
                'input' => 'nums = [2,7,11,15], target = 9',
                'output' => '[0,1]',
                'explanation' => 'Because nums[0] + nums[1] == 9, we return [0, 1].',
            ],
            [
                'input' => 'nums = [3,2,4], target = 6',
                'output' => '[1,2]',
                'explanation' => 'nums[1] + nums[2] == 6, so the indices 1 and 2 form the answer.',
            ],
            [
                'input' => 'nums = [3,3], target = 6',
                'output' => '[0,1]',
                'explanation' => 'The same value may appear twice as long as the two occurrences are at different indices.',
            ],
        ],
        'constraints' => [
            '2 <= nums.length <= 100,000',
            '-1,000,000,000 <= nums[i] <= 1,000,000,000',
            '-1,000,000,000 <= target <= 1,000,000,000',
            'Exactly one valid answer exists for every test case.',
        ],
        'follow_up' => 'Can you design an algorithm faster than O(n^2)? What is the extra memory cost? Could you solve the problem without modifying the input array?',
        'editorial' => [
            'insight' => 'When you need target - nums[i], you do not have to rescan the array. You only need to know whether that value has already appeared.',
            'steps' => [
                'Create an empty hash map from each value to the index where it appeared.',
                'Walk the array once from left to right.',
                'Before inserting nums[i], check whether target - nums[i] is already in the map.',
                'If it is, return both indices. Otherwise store nums[i] mapped to i and continue.',
            ],
            'time_complexity' => 'O(n)',
            'space_complexity' => 'O(n)',
        ],
        'starter_code' => [
            'cpp' => <<<'CPP'
#include <vector>
using namespace std;

class Solution {
public:
    vector<int> twoSum(vector<int>& nums, int target) {
        // TODO: return the indices of the two numbers that add up to target.
    }
};
CPP,
            'python' => <<<'PYTHON'
class Solution:
    def twoSum(self, nums: list[int], target: int) -> list[int]:
        # TODO: return the indices of the two numbers that add up to target.
        pass
PYTHON,
            'javascript' => <<<'JAVASCRIPT'
/**
 * @param {number[]} nums
 * @param {number} target
 * @return {number[]}
 */
function twoSum(nums, target) {
    // TODO: return the indices of the two numbers that add up to target.
}
JAVASCRIPT,
        ],
        'testing' => [
            'judge' => 'Your submission is compiled or interpreted, then executed once per test file. For each test file the runner passes the input on standard input and compares your standard output with the expected output after trimming trailing whitespace on every line. A non-zero exit code, a timeout, or a memory limit breach immediately fails the submission. Because several valid outputs may exist for this problem, the checker parses your returned indices and verifies the sum instead of comparing the output byte for byte.',
            'time_limit' => '2 seconds per test file',
            'memory_limit' => '256 MB',
            'languages' => ['C++ 20', 'Python 3.12', 'JavaScript (Node 22)'],
            'notes' => [
                'Read all input from standard input and write the two indices to standard output separated by a single space.',
                'The indices may be printed in either order; the checker normalises them before validating.',
                'Do not print any debug output, because extra lines are treated as a wrong answer.',
                'The input may contain negative numbers, so avoid unsigned integer types for the values.',
                'Solutions slower than O(n^2) will time out on the largest hidden test files.',
            ],
            'sample_tests' => [
                [
                    'name' => 'Basic pair',
                    'input' => "nums = [2,7,11,15]\ntarget = 9",
                    'expected' => '[0, 1]',
                    'hidden' => false,
                ],
                [
                    'name' => 'Duplicate values',
                    'input' => "nums = [3,3]\ntarget = 6",
                    'expected' => '[0, 1]',
                    'hidden' => false,
                ],
                [
                    'name' => 'Answer late in the array',
                    'input' => "nums = [1,5,3,8,2]\ntarget = 10",
                    'expected' => '[3, 4]',
                    'hidden' => true,
                ],
                [
                    'name' => 'Negative numbers',
                    'input' => "nums = [-3,4,3,90]\ntarget = 0",
                    'expected' => '[0, 2]',
                    'hidden' => true,
                ],
            ],
        ],
        'hints' => [
            'Start with the brute force solution and identify the repeated work.',
            'If you already know the value you need, you only have to ask whether it appeared earlier.',
            'A hash map from value to index turns the inner scan into a constant-time lookup.',
            'Check the map before inserting the current element so you never pair an element with itself.',
        ],
    ],
    [
        'id' => 'valid-parentheses',
        'number' => 20,
        'title' => 'Valid Parentheses',
        'difficulty' => 'Easy',
        'acceptance_rate' => 41.8,
        'tags' => ['String', 'Stack'],
        'summary' => 'Decide whether a string of bracket characters is correctly nested and closed.',
        'description' => [
            'Given a string s containing only the characters (, ), {, }, [ and ], determine whether the string is valid. A string is valid when every opening bracket is closed by the matching type of bracket, brackets are closed in the correct order, and every closing bracket has a corresponding opening bracket that appears before it.',
            'The natural way to think about nesting is to process the string from left to right while keeping a stack of opening brackets. When you see an opening bracket, push it. When you see a closing bracket, the top of the stack must contain its matching opening bracket. If the stack is empty, the closing bracket has nothing to match. If the top does not match, the nesting is wrong. After processing the whole string, the stack must be empty, otherwise some opening brackets were never closed.',
            'This stack approach mirrors how compilers and text editors validate delimiters, and it runs in O(n) time because every character is pushed or popped at most once. The stack can grow to at most n entries, so the extra space is also O(n) in the worst case, for example when the string contains only opening brackets.',
            'Several edge cases deserve attention. An empty string is considered valid, although the stated constraints use a minimum length of one. A string that starts with a closing bracket fails immediately. A string made of a single opening bracket fails at the end because the stack is not empty. Mismatched pairs such as (] must fail even though the counts of opening and closing brackets are equal.',
            'Return true when the string is valid and false otherwise. The judge expects the literal words true or false, and it does not accept variations such as yes, no, 1 or 0.',
        ],
        'examples' => [
            [
                'input' => 's = "()"',
                'output' => 'true',
            ],
            [
                'input' => 's = "()[]{}"',
                'output' => 'true',
                'explanation' => 'Three independent pairs appear back to back and each one is closed correctly.',
            ],
            [
                'input' => 's = "(]"',
                'output' => 'false',
                'explanation' => 'The stack expects a ) but receives a ], so the types do not match.',
            ],
            [
                'input' => 's = "([)]"',
                'output' => 'false',
                'explanation' => 'The brackets are closed out of order, which is not allowed.',
            ],
        ],
        'constraints' => [
            '1 <= s.length <= 10,000',
            's consists only of the characters (, ), {, }, [ and ].',
        ],
        'follow_up' => 'Can you adapt the solution to support angle brackets such as < and >? How would you report the position of the first invalid bracket instead of just returning false?',
        'editorial' => [
            'insight' => 'Brackets close in the reverse order they were opened, which is exactly the last-in-first-out behaviour of a stack.',
            'steps' => [
                'Push every opening bracket onto a stack.',
                'For a closing bracket, check that the stack is not empty and that its top is the matching opener.',
                'Pop the opener when it matches; otherwise return false immediately.',
                'After processing the whole string, return true only when the stack is empty.',
            ],
            'time_complexity' => 'O(n)',
            'space_complexity' => 'O(n)',
        ],
        'starter_code' => [
            'cpp' => <<<'CPP'
#include <string>
using namespace std;

class Solution {
public:
    bool isValid(string s) {
        // TODO: return true when every bracket is correctly nested.
    }
};
CPP,
            'python' => <<<'PYTHON'
class Solution:
    def isValid(self, s: str) -> bool:
        # TODO: return True when every bracket is correctly nested.
        pass
PYTHON,
            'javascript' => <<<'JAVASCRIPT'
/**
 * @param {string} s
 * @return {boolean}
 */
function isValid(s) {
    // TODO: return true when every bracket is correctly nested.
}
JAVASCRIPT,
        ],
        'testing' => [
            'judge' => 'The runner feeds one bracket string per test file on standard input and compares the first token of your output with the expected true or false token. Leading and trailing whitespace is ignored, but any additional text makes the answer invalid.',
            'time_limit' => '1 second per test file',
            'memory_limit' => '256 MB',
            'languages' => ['C++ 20', 'Python 3.12', 'JavaScript (Node 22)'],
            'notes' => [
                'The input string does not contain spaces, so it can be read with a single token read.',
                'Return exactly true or false; capitalised variants are marked as wrong answers.',
                'Hidden tests include deeply nested strings with up to 10,000 characters.',
                'A recursive solution may exceed the call stack depth on the largest inputs.',
            ],
            'sample_tests' => [
                [
                    'name' => 'Single pair',
                    'input' => '()',
                    'expected' => 'true',
                    'hidden' => false,
                ],
                [
                    'name' => 'Mismatched types',
                    'input' => '(]',
                    'expected' => 'false',
                    'hidden' => false,
                ],
                [
                    'name' => 'Interleaved pairs',
                    'input' => '([)]',
                    'expected' => 'false',
                    'hidden' => false,
                ],
                [
                    'name' => 'Deeply nested',
                    'input' => '(((((((((())))))))))',
                    'expected' => 'true',
                    'hidden' => true,
                ],
            ],
        ],
        'hints' => [
            'The most recently opened bracket must be the first one to close.',
            'That last-in-first-out rule is exactly what a stack provides.',
            'Store the expected closing bracket on the stack, or store the opening bracket and compare pairs.',
            'Remember to check that the stack is empty after the loop finishes.',
        ],
    ],
    [
        'id' => 'merge-intervals',
        'number' => 56,
        'title' => 'Merge Intervals',
        'difficulty' => 'Medium',
        'acceptance_rate' => 48.9,
        'tags' => ['Array', 'Sorting'],
        'summary' => 'Combine all overlapping intervals in a list into a minimal set of disjoint intervals.',
        'description' => [
            'You are given an array of intervals where each interval is a pair [start, end] with start less than or equal to end. Two intervals overlap when they share at least one point, so [1,4] and [4,5] overlap at the single point 4. Merge every group of overlapping intervals and return the resulting list of non-overlapping intervals that covers exactly the same ranges as the input. The output must be sorted in ascending order of start values.',
            'The key observation is that overlapping intervals become easy to handle once the list is sorted by start. After sorting, any interval that overlaps the previous merged interval must also have a start less than or equal to the current merged end. Therefore a single linear scan is enough: keep a result list whose last element is the interval currently being extended, and compare each new interval with that last element.',
            'If the new interval starts after the end of the last merged interval, there is a gap, so append the new interval as a fresh entry. Otherwise the intervals overlap or touch, and the merged end simply becomes the maximum of the two ends. Taking the maximum is important because a later interval may be completely contained inside the current one, in which case the end does not change.',
            'Sorting dominates the running time, so the solution is O(n log n) overall with O(n) additional space for the output. A naive approach that repeatedly scans for overlaps can degrade to O(n^2) and is not fast enough for the largest tests.',
            'The judge compares intervals as exact integer pairs and is sensitive to their order. Return the merged intervals sorted by start, and do not include any interval that is completely covered by another interval.',
        ],
        'examples' => [
            [
                'input' => 'intervals = [[1,3],[2,6],[8,10],[15,18]]',
                'output' => '[[1,6],[8,10],[15,18]]',
                'explanation' => 'Intervals [1,3] and [2,6] overlap, producing [1,6]. The remaining intervals do not touch.',
            ],
            [
                'input' => 'intervals = [[1,4],[4,5]]',
                'output' => '[[1,5]]',
                'explanation' => 'The intervals share the endpoint 4, so they are merged into one.',
            ],
            [
                'input' => 'intervals = [[1,4],[0,4]]',
                'output' => '[[0,4]]',
                'explanation' => 'After sorting by start, [0,4] covers [1,4] completely.',
            ],
        ],
        'constraints' => [
            '1 <= intervals.length <= 10,000',
            'intervals[i].length == 2',
            '0 <= start <= end <= 10,000',
        ],
        'follow_up' => 'Can you merge the intervals in a single pass if the input is already sorted by start? What changes if intervals are half-open, meaning [1,4) and [4,5) should not merge?',
        'editorial' => [
            'insight' => 'After sorting by start, a new interval can only overlap the most recently merged interval.',
            'steps' => [
                'Sort the intervals by their starting point.',
                'Start the result with the first interval.',
                'For each next interval, compare its start with the current merged end.',
                'If the start is at most the end, extend the end to the maximum of both ends. Otherwise append the interval to the result.',
            ],
            'time_complexity' => 'O(n log n)',
            'space_complexity' => 'O(n)',
        ],
        'starter_code' => [
            'cpp' => <<<'CPP'
#include <vector>
using namespace std;

class Solution {
public:
    vector<vector<int>> merge(vector<vector<int>>& intervals) {
        // TODO: return the merged, sorted list of intervals.
    }
};
CPP,
            'python' => <<<'PYTHON'
class Solution:
    def merge(self, intervals: list[list[int]]) -> list[list[int]]:
        # TODO: return the merged, sorted list of intervals.
        pass
PYTHON,
            'javascript' => <<<'JAVASCRIPT'
/**
 * @param {number[][]} intervals
 * @return {number[][]}
 */
function merge(intervals) {
    // TODO: return the merged, sorted list of intervals.
}
JAVASCRIPT,
        ],
        'testing' => [
            'judge' => 'Each test file contains one interval list. Your output is parsed as a list of pairs and compared with the expected merged list. The comparison is order sensitive, so the intervals must appear sorted by their start value.',
            'time_limit' => '2 seconds per test file',
            'memory_limit' => '256 MB',
            'languages' => ['C++ 20', 'Python 3.12', 'JavaScript (Node 22)'],
            'notes' => [
                'Intervals that only touch at an endpoint count as overlapping and must be merged.',
                'Do not mutate the input if your language passes it by reference; create a copy before sorting.',
                'Hidden tests contain up to 10,000 intervals and many fully nested intervals.',
                'An O(n^2) merging strategy will time out.',
            ],
            'sample_tests' => [
                [
                    'name' => 'Overlapping chain',
                    'input' => 'intervals = [[1,3],[2,6],[8,10],[15,18]]',
                    'expected' => '[[1,6],[8,10],[15,18]]',
                    'hidden' => false,
                ],
                [
                    'name' => 'Touching endpoints',
                    'input' => 'intervals = [[1,4],[4,5]]',
                    'expected' => '[[1,5]]',
                    'hidden' => false,
                ],
                [
                    'name' => 'Nested interval',
                    'input' => 'intervals = [[1,10],[2,3],[4,8]]',
                    'expected' => '[[1,10]]',
                    'hidden' => true,
                ],
                [
                    'name' => 'Unsorted input',
                    'input' => 'intervals = [[8,10],[1,3],[2,6],[15,18]]',
                    'expected' => '[[1,6],[8,10],[15,18]]',
                    'hidden' => true,
                ],
            ],
        ],
        'hints' => [
            'Try sorting the intervals by their starting point first.',
            'After sorting, only the last interval in the result can overlap the next interval.',
            'When the next start is at most the current end, extend the current end to the maximum of both ends.',
            'Otherwise, start a new interval in the result list.',
        ],
    ],
    [
        'id' => 'lru-cache',
        'number' => 146,
        'title' => 'LRU Cache',
        'difficulty' => 'Medium',
        'acceptance_rate' => 43.1,
        'tags' => ['Hash Table', 'Linked List', 'Design'],
        'summary' => 'Implement a fixed-capacity cache that evicts the least recently used key.',
        'description' => [
            'Design a data structure that behaves like a least-recently-used cache. The cache has a fixed positive capacity and supports two operations. get(key) returns the value associated with the key if it exists, or -1 when the key is absent. put(key, value) inserts or updates the value for the key, and when the number of stored keys would exceed the capacity it evicts the key that was used least recently.',
            'Both get and put must run in O(1) average time. A hash map alone gives constant-time lookup but cannot tell you which key is the least recently used. A linked list alone preserves usage order but requires linear time to find a key. The standard solution combines the two: the hash map stores key to node pointers, and a doubly linked list stores the nodes in recency order.',
            'Keep the most recently used node at the head of the list and the least recently used node at the tail. Every successful get moves the accessed node to the head. Every put either updates an existing node and moves it to the head, or creates a new node at the head. When the size exceeds capacity, remove the tail node from both the list and the map. Using a doubly linked list is essential because removal and insertion must both be O(1).',
            'Order of operations matters when the cache is full and you put a key that already exists. An existing key is an update, not an eviction, so the size does not grow and nothing is removed. A common bug is to evict before checking whether the key is already present, which incorrectly removes a different entry.',
            'The testing harness does not call your class directly. Instead, it plays a sequence of operations encoded as strings and records the outputs of the get calls. For example, the sequence put(1, 1), put(2, 2), get(1), put(3, 3), get(2) must produce the outputs 1 and -1, because inserting key 3 evicts key 2, which was the least recently used at that moment.',
        ],
        'examples' => [
            [
                'input' => 'capacity = 2, operations = [put(1,1), put(2,2), get(1), put(3,3), get(2), put(4,4), get(1), get(3), get(4)]',
                'output' => '[1, -1, -1, 3, 4]',
                'explanation' => 'Key 2 is evicted when 3 is inserted; key 1 is evicted when 4 is inserted after being refreshed by get(1).',
            ],
            [
                'input' => 'capacity = 1, operations = [put(1,10), put(2,20), get(1), get(2)]',
                'output' => '[-1, 20]',
                'explanation' => 'With capacity one, inserting key 2 evicts key 1 immediately.',
            ],
            [
                'input' => 'capacity = 2, operations = [put(1,1), put(1,2), get(1)]',
                'output' => '[2]',
                'explanation' => 'Updating an existing key does not create a second entry and does not evict anything.',
            ],
        ],
        'constraints' => [
            '1 <= capacity <= 3,000',
            '0 <= key <= 10,000',
            '0 <= value <= 100,000',
            'At most 200,000 operations are performed in total.',
            'Every get and put call must run in O(1) average time.',
        ],
        'follow_up' => 'How would you make the cache thread-safe? Can you extend the design so that entries also expire after a time-to-live?',
        'editorial' => [
            'insight' => 'A hash map finds a key in O(1), and a doubly linked list moves any node to the front in O(1). Use both structures together.',
            'steps' => [
                'Store a key to node mapping in a hash map.',
                'Keep a doubly linked list with the most recently used node at the head and the least recently used node at the tail.',
                'On get, return -1 when the key is missing. Otherwise move the node to the head and return its value.',
                'On put, update or insert the node at the head, then remove the tail node when the size exceeds capacity.',
            ],
            'time_complexity' => 'O(1) per operation',
            'space_complexity' => 'O(capacity)',
        ],
        'starter_code' => [
            'cpp' => <<<'CPP'
#include <unordered_map>
using namespace std;

class LRUCache {
public:
    LRUCache(int capacity) {
        // TODO: initialise the cache with the given capacity.
    }

    int get(int key) {
        // TODO: return the value, or -1 when the key is missing.
    }

    void put(int key, int value) {
        // TODO: insert or update the key and evict when over capacity.
    }
};
CPP,
            'python' => <<<'PYTHON'
class LRUCache:
    def __init__(self, capacity: int):
        # TODO: initialise the cache with the given capacity.
        pass

    def get(self, key: int) -> int:
        # TODO: return the value, or -1 when the key is missing.
        pass

    def put(self, key: int, value: int) -> None:
        # TODO: insert or update the key and evict when over capacity.
        pass
PYTHON,
            'javascript' => <<<'JAVASCRIPT'
class LRUCache {
    /**
     * @param {number} capacity
     */
    constructor(capacity) {
        // TODO: initialise the cache with the given capacity.
    }

    /**
     * @param {number} key
     * @return {number}
     */
    get(key) {
        // TODO: return the value, or -1 when the key is missing.
    }

    /**
     * @param {number} key
     * @param {number} value
     * @return {void}
     */
    put(key, value) {
        // TODO: insert or update the key and evict when over capacity.
    }
}
JAVASCRIPT,
        ],
        'testing' => [
            'judge' => 'The runner instantiates your class with the given capacity and replays the operation list in order. It collects every value returned by get and compares that list with the expected list. Constructors and put calls must not print anything.',
            'time_limit' => '3 seconds per test file',
            'memory_limit' => '256 MB',
            'languages' => ['C++ 20', 'Python 3.12', 'JavaScript (Node 22)'],
            'notes' => [
                'Operations are guaranteed to be valid; capacity and keys stay within the stated bounds.',
                'A get on a missing key returns -1 and does not change the recency order.',
                'A put on an existing key updates the value and refreshes its recency.',
                'Using a built-in ordered map such as std::map or a linear scan will time out on the largest tests.',
                'Node handles for an intrusive linked list must also be removed from the map when evicted.',
            ],
            'sample_tests' => [
                [
                    'name' => 'Classic eviction sequence',
                    'input' => "capacity = 2\nput(1,1)\nput(2,2)\nget(1)\nput(3,3)\nget(2)",
                    'expected' => '[1, -1]',
                    'hidden' => false,
                ],
                [
                    'name' => 'Capacity of one',
                    'input' => "capacity = 1\nput(1,10)\nput(2,20)\nget(1)\nget(2)",
                    'expected' => '[-1, 20]',
                    'hidden' => false,
                ],
                [
                    'name' => 'Update without eviction',
                    'input' => "capacity = 2\nput(1,1)\nput(1,2)\nget(1)",
                    'expected' => '[2]',
                    'hidden' => true,
                ],
                [
                    'name' => 'Recency refresh by get',
                    'input' => "capacity = 2\nput(1,1)\nput(2,2)\nget(1)\nput(3,3)\nget(1)\nget(2)",
                    'expected' => '[1, 1, -1]',
                    'hidden' => true,
                ],
            ],
        ],
        'hints' => [
            'You need O(1) lookup by key and O(1) access to the least recently used entry.',
            'A doubly linked list gives O(1) removal and insertion at both ends.',
            'Pointers stored in a hash map let you remove any node without scanning the list.',
            'Refresh the recency of a key on every successful get and on every put, including updates.',
            'Insert the new entry first and evict only after the size exceeds capacity, being careful not to evict the entry you just inserted.',
        ],
    ],
    [
        'id' => 'course-schedule',
        'number' => 207,
        'title' => 'Course Schedule',
        'difficulty' => 'Medium',
        'acceptance_rate' => 47.6,
        'tags' => ['Graph', 'Topological Sort', 'DFS', 'BFS'],
        'summary' => 'Determine whether all courses can be finished given a list of prerequisites.',
        'description' => [
            'There are numCourses courses labelled from 0 to numCourses - 1. You are given a list of prerequisite pairs where each pair [a, b] means that course b must be completed before course a. Return true if it is possible to finish every course, and false otherwise.',
            'The prerequisites form a directed graph in which an edge points from b to a. Finishing all courses is possible exactly when this graph contains no directed cycle. If a cycle exists, every course on the cycle waits for another course on the same cycle, so none of them can ever be started.',
            'One way to detect a cycle is a depth-first search with three colours. Unvisited nodes are white, nodes on the current recursion path are grey, and fully processed nodes are black. If the search ever reaches a grey node, it has found a back edge and therefore a cycle. Marking a node black after all of its neighbours finish prevents repeated work and keeps the algorithm linear in the number of edges and nodes.',
            'An alternative and often simpler approach is Kahn algorithm for topological sorting. Compute the in-degree of every node, push all nodes with in-degree zero into a queue, and repeatedly remove a node, appending it to the order and decreasing the in-degree of its neighbours. If the final order contains every course, the graph is acyclic. If some courses remain unprocessed, those courses are part of or behind a cycle.',
            'Both approaches run in O(V + E) time and use O(V + E) memory, which fits comfortably within the limits. Be careful when the same prerequisite pair appears more than once, and remember that numCourses may be larger than the number of nodes mentioned in the prerequisites.',
        ],
        'examples' => [
            [
                'input' => 'numCourses = 2, prerequisites = [[1,0]]',
                'output' => 'true',
                'explanation' => 'Course 0 must be taken first, then course 1. There is no cycle.',
            ],
            [
                'input' => 'numCourses = 2, prerequisites = [[1,0],[0,1]]',
                'output' => 'false',
                'explanation' => 'Courses 0 and 1 depend on each other, so neither can start.',
            ],
            [
                'input' => 'numCourses = 4, prerequisites = [[1,0],[2,1],[3,2]]',
                'output' => 'true',
                'explanation' => 'The courses can be taken in the order 0, 1, 2, 3.',
            ],
        ],
        'constraints' => [
            '1 <= numCourses <= 2,000',
            '0 <= prerequisites.length <= 5,000',
            'prerequisites[i].length == 2',
            '0 <= a, b < numCourses',
            'All prerequisite pairs are unique.',
        ],
        'follow_up' => 'If the answer is true, can you return one valid order in which the courses can be taken? How does the solution change when prerequisites have weights or credits?',
        'editorial' => [
            'insight' => 'Every course can be finished exactly when the prerequisite graph contains no directed cycle.',
            'steps' => [
                'Build an adjacency list and compute the in-degree of every course.',
                'Push every course with in-degree zero into a queue.',
                'Pop a course, add it to the order, and decrement the in-degree of its neighbours.',
                'Push a neighbour when its in-degree reaches zero. Return true only when all courses are counted.',
            ],
            'time_complexity' => 'O(V + E)',
            'space_complexity' => 'O(V + E)',
        ],
        'starter_code' => [
            'cpp' => <<<'CPP'
#include <vector>
using namespace std;

class Solution {
public:
    bool canFinish(int numCourses, vector<vector<int>>& prerequisites) {
        // TODO: return true when no prerequisite cycle exists.
    }
};
CPP,
            'python' => <<<'PYTHON'
class Solution:
    def canFinish(self, numCourses: int, prerequisites: list[list[int]]) -> bool:
        # TODO: return True when no prerequisite cycle exists.
        pass
PYTHON,
            'javascript' => <<<'JAVASCRIPT'
/**
 * @param {number} numCourses
 * @param {number[][]} prerequisites
 * @return {boolean}
 */
function canFinish(numCourses, prerequisites) {
    // TODO: return true when no prerequisite cycle exists.
}
JAVASCRIPT,
        ],
        'testing' => [
            'judge' => 'The runner reads the number of courses and the prerequisite list from standard input, then compares the first output token with the expected true or false. The comparison ignores surrounding whitespace only.',
            'time_limit' => '2 seconds per test file',
            'memory_limit' => '256 MB',
            'languages' => ['C++ 20', 'Python 3.12', 'JavaScript (Node 22)'],
            'notes' => [
                'The graph may be disconnected; every component must still be acyclic.',
                'Self-prerequisites such as [0,0] are possible in hidden tests and form an immediate cycle.',
                'A recursive DFS may exceed the default stack depth on a chain of 2,000 courses; an iterative DFS or BFS is safer.',
                'Prerequisite pairs are unique, so you do not need to deduplicate edges.',
            ],
            'sample_tests' => [
                [
                    'name' => 'Simple dependency',
                    'input' => 'numCourses = 2, prerequisites = [[1,0]]',
                    'expected' => 'true',
                    'hidden' => false,
                ],
                [
                    'name' => 'Two-course cycle',
                    'input' => 'numCourses = 2, prerequisites = [[1,0],[0,1]]',
                    'expected' => 'false',
                    'hidden' => false,
                ],
                [
                    'name' => 'Self loop',
                    'input' => 'numCourses = 1, prerequisites = [[0,0]]',
                    'expected' => 'false',
                    'hidden' => true,
                ],
                [
                    'name' => 'Disconnected components',
                    'input' => 'numCourses = 5, prerequisites = [[1,0],[2,1],[4,3]]',
                    'expected' => 'true',
                    'hidden' => true,
                ],
            ],
        ],
        'hints' => [
            'Model the prerequisites as a directed graph and look for a directed cycle.',
            'A DFS that tracks nodes on the current recursion path can detect a back edge.',
            'Kahn algorithm counts how many prerequisites each course still has pending.',
            'If the topological order contains fewer than numCourses nodes, a cycle was found.',
        ],
    ],
    [
        'id' => 'trapping-rain-water',
        'number' => 42,
        'title' => 'Trapping Rain Water',
        'difficulty' => 'Hard',
        'acceptance_rate' => 62.7,
        'tags' => ['Array', 'Two Pointers', 'Dynamic Programming', 'Stack'],
        'summary' => 'Compute how much rainwater is trapped between bars of varying heights.',
        'description' => [
            'You are given an array of non-negative integers representing an elevation map, where each value is the height of a bar with width one. After it rains, water collects in the valleys between taller bars. Return the total number of units of water that can be trapped.',
            'The amount of water above a single bar at index i is determined by the shorter of the tallest bar to its left and the tallest bar to its right, minus the height of the bar itself. If either side has no taller bar, no water can be held above that position. A direct implementation can precompute prefix maxima and suffix maxima, then sum the contribution of every index in O(n) time with O(n) extra space.',
            'The same idea can be implemented with two pointers and O(1) extra space. Place one pointer at each end of the array and maintain the maximum height seen from the left and from the right. Always advance the pointer whose current maximum is smaller, because that side is the limiting wall. The water contribution at that pointer is the current side maximum minus its height, clamped to zero. Every bar is visited once, so the running time stays O(n).',
            'A monotonic stack offers a third perspective: it keeps indices of bars in decreasing height order, and whenever a taller bar appears it closes a valley and computes the water held between the two walls. This approach is a useful test of understanding but is more error-prone than the two-pointer method, especially for equal heights.',
            'The total can exceed 32-bit integer range when the array is long and the heights are large, so use a 64-bit integer for the accumulator in C++ and Java. The algorithm should not modify the input array, and it must handle arrays with zero bars or bars of height zero.',
        ],
        'examples' => [
            [
                'input' => 'height = [0,1,0,2,1,0,1,3,2,1,2,1]',
                'output' => '6',
                'explanation' => 'The elevation map traps six units of water in the valleys between the tall bars.',
            ],
            [
                'input' => 'height = [4,2,0,3,2,5]',
                'output' => '9',
                'explanation' => 'Water is trapped above the bars of heights 2, 0, 3 and 2, and above parts of the surrounding slopes.',
            ],
            [
                'input' => 'height = [1,0,1]',
                'output' => '1',
                'explanation' => 'The middle bar is lower than both neighbours, so it holds exactly one unit.',
            ],
        ],
        'constraints' => [
            '1 <= height.length <= 20,000',
            '0 <= height[i] <= 100,000',
            'The total answer fits in a signed 64-bit integer.',
        ],
        'follow_up' => 'Can you solve the problem in O(1) extra space? What changes if bars have width greater than one? Can you adapt the solution to return the maximum water trapped above any single bar?',
        'editorial' => [
            'insight' => 'The water above a bar is limited by the shorter of the tallest bars on its left and its right.',
            'steps' => [
                'Place one pointer at each end and track the maximum height seen from both sides.',
                'Advance the pointer whose side has the smaller maximum, because that side is the limiting wall.',
                'Add the side maximum minus the current height, clamped to zero, to the total.',
                'Update that side maximum and repeat until the pointers meet.',
            ],
            'time_complexity' => 'O(n)',
            'space_complexity' => 'O(1)',
        ],
        'starter_code' => [
            'cpp' => <<<'CPP'
#include <vector>
using namespace std;

class Solution {
public:
    long long trap(vector<int>& height) {
        // TODO: return the total units of trapped rainwater.
    }
};
CPP,
            'python' => <<<'PYTHON'
class Solution:
    def trap(self, height: list[int]) -> int:
        # TODO: return the total units of trapped rainwater.
        pass
PYTHON,
            'javascript' => <<<'JAVASCRIPT'
/**
 * @param {number[]} height
 * @return {number}
 */
function trap(height) {
    // TODO: return the total units of trapped rainwater.
}
JAVASCRIPT,
        ],
        'testing' => [
            'judge' => 'The runner reads one height array per test file and compares your first output token with the expected integer. Large tests verify that you use a wide enough integer type for the accumulator.',
            'time_limit' => '2 seconds per test file',
            'memory_limit' => '256 MB',
            'languages' => ['C++ 20', 'Python 3.12', 'JavaScript (Node 22)'],
            'notes' => [
                'The answer for the largest hidden test can exceed 2^31, so a 32-bit accumulator may overflow.',
                'Empty arrays and arrays with a single bar produce the answer 0.',
                'A quadratic solution that scans left and right for every bar will time out.',
                'Do not print the intermediate prefix or suffix arrays; only the final total is expected.',
            ],
            'sample_tests' => [
                [
                    'name' => 'Classic elevation map',
                    'input' => 'height = [0,1,0,2,1,0,1,3,2,1,2,1]',
                    'expected' => '6',
                    'hidden' => false,
                ],
                [
                    'name' => 'Single valley',
                    'input' => 'height = [1,0,1]',
                    'expected' => '1',
                    'hidden' => false,
                ],
                [
                    'name' => 'Monotonic slope',
                    'input' => 'height = [1,2,3,4,5]',
                    'expected' => '0',
                    'hidden' => true,
                ],
                [
                    'name' => 'Wide basin',
                    'input' => 'height = [100000,0,0,0,100000]',
                    'expected' => '300000',
                    'hidden' => true,
                ],
            ],
        ],
        'hints' => [
            'Water above a bar depends on the tallest bar on each side.',
            'Precomputing prefix and suffix maxima gives an O(n) solution immediately.',
            'To remove the extra arrays, advance the side with the smaller running maximum.',
            'The water contribution at the advanced pointer is its side maximum minus its own height.',
        ],
    ],
    [
        'id' => 'median-of-two-sorted-arrays',
        'number' => 4,
        'title' => 'Median of Two Sorted Arrays',
        'difficulty' => 'Hard',
        'acceptance_rate' => 42.4,
        'tags' => ['Array', 'Binary Search', 'Divide and Conquer'],
        'summary' => 'Find the median of two sorted arrays in logarithmic time without merging them.',
        'description' => [
            'Given two sorted arrays nums1 and nums2 with sizes m and n, return the median of the combined multiset of values. The median is the middle value when the total number of elements is odd, and the average of the two middle values when the total is even. The combined array may have up to 2,000,000 elements.',
            'A merge-based solution is easy to write: walk both arrays with two pointers and stop at the middle positions. That costs O(m + n) time, which is linear and may be rejected by the strict time limit. The problem is designed to push for an O(log(min(m, n))) solution based on binary search over a partition of the shorter array.',
            'Imagine cutting the combined sorted sequence into a left half and a right half of roughly equal size. Each half is made of a prefix of nums1 and a prefix of nums2. If i elements come from nums1 and j elements come from nums2, then i + j must equal (m + n + 1) / 2 using integer division. The partition is correct when every element on the left is at most every element on the right, which reduces to two comparisons at the four boundary values.',
            'Binary search the value of i in the range 0 to m, always using the shorter array to keep the search space small. If the left value from nums1 is greater than the right value from nums2, the cut is too far right in nums1, so move it left. If the left value from nums2 is greater than the right value from nums1, the cut is too far left, so move it right. Otherwise the partition is valid and the median can be read directly from the boundary values.',
            'Treat out-of-range indices as negative and positive infinity, which keeps the comparison logic uniform and avoids separate branches for empty prefixes. Use doubles for the final average when the total length is even, and be careful with integer division and negative values. The constraints allow empty arrays, but not both at the same time.',
        ],
        'examples' => [
            [
                'input' => 'nums1 = [1,3], nums2 = [2]',
                'output' => '2.00000',
                'explanation' => 'The merged array is [1,2,3] and its middle element is 2.',
            ],
            [
                'input' => 'nums1 = [1,2], nums2 = [3,4]',
                'output' => '2.50000',
                'explanation' => 'The merged array is [1,2,3,4], and the average of 2 and 3 is 2.5.',
            ],
            [
                'input' => 'nums1 = [], nums2 = [1]',
                'output' => '1.00000',
                'explanation' => 'An empty array contributes nothing, so the median is the only element of the second array.',
            ],
        ],
        'constraints' => [
            '0 <= m <= 1,000,000',
            '0 <= n <= 1,000,000',
            '1 <= m + n <= 2,000,000',
            '-1,000,000 <= nums1[i], nums2[i] <= 1,000,000',
            'Both input arrays are sorted in non-decreasing order.',
        ],
        'follow_up' => 'Can you find the k-th smallest element of the two arrays with the same partition idea? How would the approach extend to three sorted arrays?',
        'editorial' => [
            'insight' => 'The median is a partition: both halves contain the same number of elements, and every value on the left is at most every value on the right.',
            'steps' => [
                'Binary search the cut i in the shorter array.',
                'Derive j, the number of elements taken from the second array, from the required left-half size.',
                'If the left value from the first array exceeds the right value from the second, move i left. If the opposite happens, move i right.',
                'When the partition is valid, read the median from the four boundary values, treating missing boundaries as negative or positive infinity.',
            ],
            'time_complexity' => 'O(log(min(m, n)))',
            'space_complexity' => 'O(1)',
        ],
        'starter_code' => [
            'cpp' => <<<'CPP'
#include <vector>
using namespace std;

class Solution {
public:
    double findMedianSortedArrays(vector<int>& nums1, vector<int>& nums2) {
        // TODO: return the median of the combined sorted values.
    }
};
CPP,
            'python' => <<<'PYTHON'
class Solution:
    def findMedianSortedArrays(self, nums1: list[int], nums2: list[int]) -> float:
        # TODO: return the median of the combined sorted values.
        pass
PYTHON,
            'javascript' => <<<'JAVASCRIPT'
/**
 * @param {number[]} nums1
 * @param {number[]} nums2
 * @return {number}
 */
function findMedianSortedArrays(nums1, nums2) {
    // TODO: return the median of the combined sorted values.
}
JAVASCRIPT,
        ],
        'testing' => [
            'judge' => 'The runner reads both arrays from standard input and compares your output with the expected median. Answers are accepted when the absolute or relative error is at most 1e-5, so printing five or more decimal places is safe.',
            'time_limit' => '2 seconds per test file',
            'memory_limit' => '512 MB',
            'languages' => ['C++ 20', 'Python 3.12', 'JavaScript (Node 22)'],
            'notes' => [
                'Print a floating point number with at least five digits after the decimal point.',
                'Both arrays may be empty individually, but never at the same time.',
                'The total length can reach two million, so allocating a merged array is expensive.',
                'A linear merge may pass small tests but will time out on the largest hidden ones.',
                'Duplicate values are common; compare with <= when validating the partition.',
            ],
            'sample_tests' => [
                [
                    'name' => 'Odd total length',
                    'input' => "nums1 = [1,3]\nnums2 = [2]",
                    'expected' => '2.00000',
                    'hidden' => false,
                ],
                [
                    'name' => 'Even total length',
                    'input' => "nums1 = [1,2]\nnums2 = [3,4]",
                    'expected' => '2.50000',
                    'hidden' => false,
                ],
                [
                    'name' => 'One empty array',
                    'input' => "nums1 = []\nnums2 = [1]",
                    'expected' => '1.00000',
                    'hidden' => true,
                ],
                [
                    'name' => 'Negative values',
                    'input' => "nums1 = [-5,-3,-1]\nnums2 = [-4,-2]",
                    'expected' => '-2.50000',
                    'hidden' => true,
                ],
            ],
        ],
        'hints' => [
            'The median splits the combined data into a left half and a right half of known sizes.',
            'If you choose how many elements come from the first array, the count from the second array is fixed.',
            'A valid cut has every left element less than or equal to every right element.',
            'Binary search the cut in the shorter array and treat missing boundaries as infinite.',
        ],
    ],
    [
        'id' => 'word-ladder',
        'number' => 127,
        'title' => 'Word Ladder',
        'difficulty' => 'Hard',
        'acceptance_rate' => 41.2,
        'tags' => ['Hash Table', 'String', 'BFS'],
        'summary' => 'Find the length of the shortest transformation sequence between two words.',
        'description' => [
            'A transformation sequence from beginWord to endWord is a list of words that starts with beginWord, ends with endWord, and in which every consecutive pair of words differs by exactly one letter. Every intermediate word must exist in the given wordList, and words may not be reused within the same sequence. Return the number of words in the shortest valid sequence, or 0 if no sequence exists.',
            'The search space is naturally a graph. Each word is a node, and an edge connects two words that differ in exactly one position. The shortest sequence is the shortest path from beginWord to endWord, so breadth-first search is the right tool. BFS explores words in increasing distance from the start, which means the first time endWord is reached the path is optimal.',
            'Naively generating all neighbours of a word costs O(L * 26) if you try every letter in every position, where L is the word length. Building the graph eagerly by comparing every pair of words costs O(N^2 * L), which is too slow when the list contains tens of thousands of words. The 26-letter trick is usually fast enough, and it avoids storing a huge adjacency list.',
            'A useful optimisation is to delete a word from the lookup set as soon as it is enqueued. Any later route that reaches the same word would be at least as long, so revisiting it cannot improve the answer. This keeps each word expanded once and turns the visited set and the dictionary into a single structure.',
            'The answer counts words rather than transitions, so the initial word contributes one to the level count. Handle the case where beginWord equals endWord by returning 1 immediately, and return 0 when endWord is not present in the word list. All words have the same length and consist of lowercase English letters only.',
        ],
        'examples' => [
            [
                'input' => 'beginWord = "hit", endWord = "cog", wordList = ["hot","dot","dog","lot","log","cog"]',
                'output' => '5',
                'explanation' => 'One shortest sequence is hit -> hot -> dot -> dog -> cog, which contains five words.',
            ],
            [
                'input' => 'beginWord = "hit", endWord = "cog", wordList = ["hot","dot","dog","lot","log"]',
                'output' => '0',
                'explanation' => 'The endWord is not in the list, so it can never be part of a transformation sequence.',
            ],
            [
                'input' => 'beginWord = "a", endWord = "c", wordList = ["a","b","c"]',
                'output' => '2',
                'explanation' => 'The sequence a -> c is valid because the two words differ by exactly one letter, even though both are in the list.',
            ],
        ],
        'constraints' => [
            '1 <= beginWord.length <= 10',
            'endWord.length == beginWord.length',
            '1 <= wordList.length <= 10,000',
            'wordList[i].length == beginWord.length',
            'All words consist of lowercase English letters.',
            'All words in wordList are distinct.',
        ],
        'follow_up' => 'Can you return every shortest sequence instead of only its length? How would you speed up the search with bidirectional BFS?',
        'editorial' => [
            'insight' => 'Words are graph nodes and one-letter differences are edges, so the shortest transformation sequence is a breadth-first search layer count.',
            'steps' => [
                'Put the word list into a hash set and return 0 early when the end word is missing.',
                'Start BFS from the begin word with a distance of one.',
                'For each word, replace every position with each of the 26 letters to generate neighbours.',
                'Return the next distance when the end word is generated. Otherwise enqueue unseen neighbours and remove them from the set.',
            ],
            'time_complexity' => 'O(N * L * 26)',
            'space_complexity' => 'O(N * L)',
        ],
        'starter_code' => [
            'cpp' => <<<'CPP'
#include <string>
#include <vector>
using namespace std;

class Solution {
public:
    int ladderLength(string beginWord, string endWord, vector<string>& wordList) {
        // TODO: return the number of words in the shortest sequence, or 0.
    }
};
CPP,
            'python' => <<<'PYTHON'
class Solution:
    def ladderLength(self, beginWord: str, endWord: str, wordList: list[str]) -> int:
        # TODO: return the number of words in the shortest sequence, or 0.
        pass
PYTHON,
            'javascript' => <<<'JAVASCRIPT'
/**
 * @param {string} beginWord
 * @param {string} endWord
 * @param {string[]} wordList
 * @return {number}
 */
function ladderLength(beginWord, endWord, wordList) {
    // TODO: return the number of words in the shortest sequence, or 0.
}
JAVASCRIPT,
        ],
        'testing' => [
            'judge' => 'The runner reads the begin word, the end word and the word list from standard input, then compares your first output token with the expected sequence length. An unreachable end word must be reported as 0.',
            'time_limit' => '3 seconds per test file',
            'memory_limit' => '256 MB',
            'languages' => ['C++ 20', 'Python 3.12', 'JavaScript (Node 22)'],
            'notes' => [
                'Words in the list are distinct, but beginWord may also appear in the list.',
                'A word may be used at most once in a sequence; the BFS visited set enforces this.',
                'Building a complete pairwise adjacency list will exceed the time limit on large inputs.',
                'The answer is the number of words in the path, not the number of one-letter edits.',
                'When endWord is absent from wordList the answer is always 0.',
            ],
            'sample_tests' => [
                [
                    'name' => 'Shortest ladder exists',
                    'input' => "beginWord = \"hit\"\nendWord = \"cog\"\nwordList = [\"hot\",\"dot\",\"dog\",\"lot\",\"log\",\"cog\"]",
                    'expected' => '5',
                    'hidden' => false,
                ],
                [
                    'name' => 'Missing end word',
                    'input' => "beginWord = \"hit\"\nendWord = \"cog\"\nwordList = [\"hot\",\"dot\",\"dog\",\"lot\",\"log\"]",
                    'expected' => '0',
                    'hidden' => false,
                ],
                [
                    'name' => 'Single-letter words',
                    'input' => "beginWord = \"a\"\nendWord = \"c\"\nwordList = [\"a\",\"b\",\"c\"]",
                    'expected' => '2',
                    'hidden' => true,
                ],
                [
                    'name' => 'Long dead end',
                    'input' => "beginWord = \"aaaa\"\nendWord = \"zzzz\"\nwordList = [\"aaab\",\"aabb\",\"abbb\",\"bbbb\",\"bbbz\",\"bbzz\",\"bzzz\"]",
                    'expected' => '0',
                    'hidden' => true,
                ],
            ],
        ],
        'hints' => [
            'Words that differ by one letter are neighbours in a graph, and you need the shortest path.',
            'Breadth-first search visits nodes in order of increasing distance.',
            'Generate neighbours on the fly by replacing each position with 26 letters.',
            'Remove a word from the dictionary when you enqueue it so it is never expanded twice.',
        ],
    ],
];
