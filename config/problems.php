<?php

return array (
  0 => 
  array (
    'id' => 'two-sum',
    'number' => 1,
    'title' => 'Two Sum',
    'difficulty' => 'Easy',
    'acceptance_rate' => 54.3,
    'tags' => 
    array (
      0 => 'Array',
      1 => 'Hash Table',
    ),
    'summary' => 'Find the two indices in an array whose values add up to a given target.',
    'description' => 
    array (
      0 => 'You are given an array of integers nums and an integer target. Your task is to find two distinct indices i and j in the array such that nums[i] + nums[j] equals target, and return them as a pair. The order of the two indices does not matter, and the problem guarantees that every input has exactly one valid answer.',
      1 => 'A straightforward solution is to examine every possible pair of indices. For each i, scan every j greater than i and check whether the two values add up to the target. This is correct but takes O(n^2) time, which becomes too slow once the array grows to 100,000 elements. During an interview, that solution is usually treated as a starting point rather than the final answer.',
      2 => 'The usual improvement is to trade memory for speed. As you walk through the array once from left to right, keep a hash map from each value you have already seen to the index where it appeared. At position i you only need to know whether target - nums[i] is already in the map. If it is, you have found both indices and can return immediately. Otherwise, store nums[i] mapped to i and continue. Each element is inserted and looked up at most once, so the whole pass runs in O(n) expected time.',
      3 => 'Pay close attention to the order of the lookup and the insertion. If you insert the current value before checking, an element could be paired with itself when target is exactly twice that value. Performing the lookup first makes the two returned indices guaranteed to be distinct. Also remember that the answer should contain the original indices, not sorted positions, so sorting the array first is a trap unless you keep track of where each value came from.',
      4 => 'The function should return an array or list with exactly two integers. If you are writing in a language with fixed-size arrays, make sure the returned container has room for both indices. The judge accepts the pair in either order, so [0, 1] and [1, 0] are both valid for the first example.',
    ),
    'examples' => 
    array (
      0 => 
      array (
        'input' => 'nums = [2,7,11,15], target = 9',
        'output' => '[0,1]',
        'explanation' => 'Because nums[0] + nums[1] == 9, we return [0, 1].',
      ),
      1 => 
      array (
        'input' => 'nums = [3,2,4], target = 6',
        'output' => '[1,2]',
        'explanation' => 'nums[1] + nums[2] == 6, so the indices 1 and 2 form the answer.',
      ),
      2 => 
      array (
        'input' => 'nums = [3,3], target = 6',
        'output' => '[0,1]',
        'explanation' => 'The same value may appear twice as long as the two occurrences are at different indices.',
      ),
    ),
    'constraints' => 
    array (
      0 => '2 <= nums.length <= 100,000',
      1 => '-1,000,000,000 <= nums[i] <= 1,000,000,000',
      2 => '-1,000,000,000 <= target <= 1,000,000,000',
      3 => 'Exactly one valid answer exists for every test case.',
    ),
    'follow_up' => 'Can you design an algorithm faster than O(n^2)? What is the extra memory cost? Could you solve the problem without modifying the input array?',
    'editorial' => 
    array (
      'insight' => 'When you need target - nums[i], you do not have to rescan the array. You only need to know whether that value has already appeared.',
      'steps' => 
      array (
        0 => 'Create an empty hash map from each value to the index where it appeared.',
        1 => 'Walk the array once from left to right.',
        2 => 'Before inserting nums[i], check whether target - nums[i] is already in the map.',
        3 => 'If it is, return both indices. Otherwise store nums[i] mapped to i and continue.',
      ),
      'time_complexity' => 'O(n)',
      'space_complexity' => 'O(n)',
    ),
    'starter_code' => 
    array (
      'cpp' => '#include <vector>
using namespace std;

class Solution {
public:
    vector<int> twoSum(vector<int>& nums, int target) {
        // TODO: return the indices of the two numbers that add up to target.
    }
};',
    ),
    'testing' => 
    array (
      'judge' => 'Your submission is compiled or interpreted, then executed once per test file. For each test file the runner passes the input on standard input and compares your standard output with the expected output after trimming trailing whitespace on every line. A non-zero exit code, a timeout, or a memory limit breach immediately fails the submission. Because several valid outputs may exist for this problem, the checker parses your returned indices and verifies the sum instead of comparing the output byte for byte.',
      'time_limit' => '2 seconds per test file',
      'memory_limit' => '256 MB',
      'languages' => 
      array (
        0 => 'C++ 20',
      ),
      'notes' => 
      array (
        0 => 'Read all input from standard input and write the two indices to standard output separated by a single space.',
        1 => 'The indices may be printed in either order; the checker normalises them before validating.',
        2 => 'Do not print any debug output, because extra lines are treated as a wrong answer.',
        3 => 'The input may contain negative numbers, so avoid unsigned integer types for the values.',
        4 => 'Solutions slower than O(n^2) will time out on the largest hidden test files.',
      ),
      'sample_tests' => 
      array (
        0 => 
        array (
          'name' => 'Basic pair',
          'input' => 'nums = [2,7,11,15]
target = 9',
          'expected' => '[0, 1]',
          'hidden' => false,
        ),
        1 => 
        array (
          'name' => 'Duplicate values',
          'input' => 'nums = [3,3]
target = 6',
          'expected' => '[0, 1]',
          'hidden' => false,
        ),
        2 => 
        array (
          'name' => 'Answer late in the array',
          'input' => 'nums = [1,5,3,8,2]
target = 10',
          'expected' => '[3, 4]',
          'hidden' => true,
        ),
        3 => 
        array (
          'name' => 'Negative numbers',
          'input' => 'nums = [-3,4,3,90]
target = 0',
          'expected' => '[0, 2]',
          'hidden' => true,
        ),
      ),
    ),
    'hints' => 
    array (
      0 => 'Start with the brute force solution and identify the repeated work.',
      1 => 'If you already know the value you need, you only have to ask whether it appeared earlier.',
      2 => 'A hash map from value to index turns the inner scan into a constant-time lookup.',
      3 => 'Check the map before inserting the current element so you never pair an element with itself.',
    ),
    'discipline' => 'DataStructure',
    'topic' => 'Hash Table',
    'companies' => 
    array (
      0 => 'Google',
      1 => 'Amazon',
      2 => 'Meta',
    ),
  ),
  1 => 
  array (
    'id' => 'valid-parentheses',
    'number' => 20,
    'title' => 'Valid Parentheses',
    'difficulty' => 'Easy',
    'acceptance_rate' => 41.8,
    'tags' => 
    array (
      0 => 'String',
      1 => 'Stack',
    ),
    'summary' => 'Decide whether a string of bracket characters is correctly nested and closed.',
    'description' => 
    array (
      0 => 'Given a string s containing only the characters (, ), {, }, [ and ], determine whether the string is valid. A string is valid when every opening bracket is closed by the matching type of bracket, brackets are closed in the correct order, and every closing bracket has a corresponding opening bracket that appears before it.',
      1 => 'The natural way to think about nesting is to process the string from left to right while keeping a stack of opening brackets. When you see an opening bracket, push it. When you see a closing bracket, the top of the stack must contain its matching opening bracket. If the stack is empty, the closing bracket has nothing to match. If the top does not match, the nesting is wrong. After processing the whole string, the stack must be empty, otherwise some opening brackets were never closed.',
      2 => 'This stack approach mirrors how compilers and text editors validate delimiters, and it runs in O(n) time because every character is pushed or popped at most once. The stack can grow to at most n entries, so the extra space is also O(n) in the worst case, for example when the string contains only opening brackets.',
      3 => 'Several edge cases deserve attention. An empty string is considered valid, although the stated constraints use a minimum length of one. A string that starts with a closing bracket fails immediately. A string made of a single opening bracket fails at the end because the stack is not empty. Mismatched pairs such as (] must fail even though the counts of opening and closing brackets are equal.',
      4 => 'Return true when the string is valid and false otherwise. The judge expects the literal words true or false, and it does not accept variations such as yes, no, 1 or 0.',
    ),
    'examples' => 
    array (
      0 => 
      array (
        'input' => 's = "()"',
        'output' => 'true',
      ),
      1 => 
      array (
        'input' => 's = "()[]{}"',
        'output' => 'true',
        'explanation' => 'Three independent pairs appear back to back and each one is closed correctly.',
      ),
      2 => 
      array (
        'input' => 's = "(]"',
        'output' => 'false',
        'explanation' => 'The stack expects a ) but receives a ], so the types do not match.',
      ),
      3 => 
      array (
        'input' => 's = "([)]"',
        'output' => 'false',
        'explanation' => 'The brackets are closed out of order, which is not allowed.',
      ),
    ),
    'constraints' => 
    array (
      0 => '1 <= s.length <= 10,000',
      1 => 's consists only of the characters (, ), {, }, [ and ].',
    ),
    'follow_up' => 'Can you adapt the solution to support angle brackets such as < and >? How would you report the position of the first invalid bracket instead of just returning false?',
    'editorial' => 
    array (
      'insight' => 'Brackets close in the reverse order they were opened, which is exactly the last-in-first-out behaviour of a stack.',
      'steps' => 
      array (
        0 => 'Push every opening bracket onto a stack.',
        1 => 'For a closing bracket, check that the stack is not empty and that its top is the matching opener.',
        2 => 'Pop the opener when it matches; otherwise return false immediately.',
        3 => 'After processing the whole string, return true only when the stack is empty.',
      ),
      'time_complexity' => 'O(n)',
      'space_complexity' => 'O(n)',
    ),
    'starter_code' => 
    array (
      'cpp' => '#include <string>
using namespace std;

class Solution {
public:
    bool isValid(string s) {
        // TODO: return true when every bracket is correctly nested.
    }
};',
    ),
    'testing' => 
    array (
      'judge' => 'The runner feeds one bracket string per test file on standard input and compares the first token of your output with the expected true or false token. Leading and trailing whitespace is ignored, but any additional text makes the answer invalid.',
      'time_limit' => '1 second per test file',
      'memory_limit' => '256 MB',
      'languages' => 
      array (
        0 => 'C++ 20',
      ),
      'notes' => 
      array (
        0 => 'The input string does not contain spaces, so it can be read with a single token read.',
        1 => 'Return exactly true or false; capitalised variants are marked as wrong answers.',
        2 => 'Hidden tests include deeply nested strings with up to 10,000 characters.',
        3 => 'A recursive solution may exceed the call stack depth on the largest inputs.',
      ),
      'sample_tests' => 
      array (
        0 => 
        array (
          'name' => 'Single pair',
          'input' => '()',
          'expected' => 'true',
          'hidden' => false,
        ),
        1 => 
        array (
          'name' => 'Mismatched types',
          'input' => '(]',
          'expected' => 'false',
          'hidden' => false,
        ),
        2 => 
        array (
          'name' => 'Interleaved pairs',
          'input' => '([)]',
          'expected' => 'false',
          'hidden' => false,
        ),
        3 => 
        array (
          'name' => 'Deeply nested',
          'input' => '(((((((((())))))))))',
          'expected' => 'true',
          'hidden' => true,
        ),
      ),
    ),
    'hints' => 
    array (
      0 => 'The most recently opened bracket must be the first one to close.',
      1 => 'That last-in-first-out rule is exactly what a stack provides.',
      2 => 'Store the expected closing bracket on the stack, or store the opening bracket and compare pairs.',
      3 => 'Remember to check that the stack is empty after the loop finishes.',
    ),
    'discipline' => 'DataStructure',
    'topic' => 'Stack',
    'companies' => 
    array (
      0 => 'Bloomberg',
      1 => 'Meta',
    ),
  ),
  2 => 
  array (
    'id' => 'merge-intervals',
    'number' => 56,
    'title' => 'Merge Intervals',
    'difficulty' => 'Medium',
    'acceptance_rate' => 48.9,
    'tags' => 
    array (
      0 => 'Array',
      1 => 'Sorting',
    ),
    'summary' => 'Combine all overlapping intervals in a list into a minimal set of disjoint intervals.',
    'description' => 
    array (
      0 => 'You are given an array of intervals where each interval is a pair [start, end] with start less than or equal to end. Two intervals overlap when they share at least one point, so [1,4] and [4,5] overlap at the single point 4. Merge every group of overlapping intervals and return the resulting list of non-overlapping intervals that covers exactly the same ranges as the input. The output must be sorted in ascending order of start values.',
      1 => 'The key observation is that overlapping intervals become easy to handle once the list is sorted by start. After sorting, any interval that overlaps the previous merged interval must also have a start less than or equal to the current merged end. Therefore a single linear scan is enough: keep a result list whose last element is the interval currently being extended, and compare each new interval with that last element.',
      2 => 'If the new interval starts after the end of the last merged interval, there is a gap, so append the new interval as a fresh entry. Otherwise the intervals overlap or touch, and the merged end simply becomes the maximum of the two ends. Taking the maximum is important because a later interval may be completely contained inside the current one, in which case the end does not change.',
      3 => 'Sorting dominates the running time, so the solution is O(n log n) overall with O(n) additional space for the output. A naive approach that repeatedly scans for overlaps can degrade to O(n^2) and is not fast enough for the largest tests.',
      4 => 'The judge compares intervals as exact integer pairs and is sensitive to their order. Return the merged intervals sorted by start, and do not include any interval that is completely covered by another interval.',
    ),
    'examples' => 
    array (
      0 => 
      array (
        'input' => 'intervals = [[1,3],[2,6],[8,10],[15,18]]',
        'output' => '[[1,6],[8,10],[15,18]]',
        'explanation' => 'Intervals [1,3] and [2,6] overlap, producing [1,6]. The remaining intervals do not touch.',
      ),
      1 => 
      array (
        'input' => 'intervals = [[1,4],[4,5]]',
        'output' => '[[1,5]]',
        'explanation' => 'The intervals share the endpoint 4, so they are merged into one.',
      ),
      2 => 
      array (
        'input' => 'intervals = [[1,4],[0,4]]',
        'output' => '[[0,4]]',
        'explanation' => 'After sorting by start, [0,4] covers [1,4] completely.',
      ),
    ),
    'constraints' => 
    array (
      0 => '1 <= intervals.length <= 10,000',
      1 => 'intervals[i].length == 2',
      2 => '0 <= start <= end <= 10,000',
    ),
    'follow_up' => 'Can you merge the intervals in a single pass if the input is already sorted by start? What changes if intervals are half-open, meaning [1,4) and [4,5) should not merge?',
    'editorial' => 
    array (
      'insight' => 'After sorting by start, a new interval can only overlap the most recently merged interval.',
      'steps' => 
      array (
        0 => 'Sort the intervals by their starting point.',
        1 => 'Start the result with the first interval.',
        2 => 'For each next interval, compare its start with the current merged end.',
        3 => 'If the start is at most the end, extend the end to the maximum of both ends. Otherwise append the interval to the result.',
      ),
      'time_complexity' => 'O(n log n)',
      'space_complexity' => 'O(n)',
    ),
    'starter_code' => 
    array (
      'cpp' => '#include <vector>
using namespace std;

class Solution {
public:
    vector<vector<int>> merge(vector<vector<int>>& intervals) {
        // TODO: return the merged, sorted list of intervals.
    }
};',
    ),
    'testing' => 
    array (
      'judge' => 'Each test file contains one interval list. Your output is parsed as a list of pairs and compared with the expected merged list. The comparison is order sensitive, so the intervals must appear sorted by their start value.',
      'time_limit' => '2 seconds per test file',
      'memory_limit' => '256 MB',
      'languages' => 
      array (
        0 => 'C++ 20',
      ),
      'notes' => 
      array (
        0 => 'Intervals that only touch at an endpoint count as overlapping and must be merged.',
        1 => 'Do not mutate the input if your language passes it by reference; create a copy before sorting.',
        2 => 'Hidden tests contain up to 10,000 intervals and many fully nested intervals.',
        3 => 'An O(n^2) merging strategy will time out.',
      ),
      'sample_tests' => 
      array (
        0 => 
        array (
          'name' => 'Overlapping chain',
          'input' => 'intervals = [[1,3],[2,6],[8,10],[15,18]]',
          'expected' => '[[1,6],[8,10],[15,18]]',
          'hidden' => false,
        ),
        1 => 
        array (
          'name' => 'Touching endpoints',
          'input' => 'intervals = [[1,4],[4,5]]',
          'expected' => '[[1,5]]',
          'hidden' => false,
        ),
        2 => 
        array (
          'name' => 'Nested interval',
          'input' => 'intervals = [[1,10],[2,3],[4,8]]',
          'expected' => '[[1,10]]',
          'hidden' => true,
        ),
        3 => 
        array (
          'name' => 'Unsorted input',
          'input' => 'intervals = [[8,10],[1,3],[2,6],[15,18]]',
          'expected' => '[[1,6],[8,10],[15,18]]',
          'hidden' => true,
        ),
      ),
    ),
    'hints' => 
    array (
      0 => 'Try sorting the intervals by their starting point first.',
      1 => 'After sorting, only the last interval in the result can overlap the next interval.',
      2 => 'When the next start is at most the current end, extend the current end to the maximum of both ends.',
      3 => 'Otherwise, start a new interval in the result list.',
    ),
    'discipline' => 'Algorithms',
    'topic' => 'Sorting',
    'companies' => 
    array (
      0 => 'Jane Street',
      1 => 'Citadel',
    ),
  ),
  3 => 
  array (
    'id' => 'lru-cache',
    'number' => 146,
    'title' => 'LRU Cache',
    'difficulty' => 'Medium',
    'acceptance_rate' => 43.1,
    'tags' => 
    array (
      0 => 'Hash Table',
      1 => 'Linked List',
      2 => 'Design',
    ),
    'summary' => 'Implement a fixed-capacity cache that evicts the least recently used key.',
    'description' => 
    array (
      0 => 'Design a data structure that behaves like a least-recently-used cache. The cache has a fixed positive capacity and supports two operations. get(key) returns the value associated with the key if it exists, or -1 when the key is absent. put(key, value) inserts or updates the value for the key, and when the number of stored keys would exceed the capacity it evicts the key that was used least recently.',
      1 => 'Both get and put must run in O(1) average time. A hash map alone gives constant-time lookup but cannot tell you which key is the least recently used. A linked list alone preserves usage order but requires linear time to find a key. The standard solution combines the two: the hash map stores key to node pointers, and a doubly linked list stores the nodes in recency order.',
      2 => 'Keep the most recently used node at the head of the list and the least recently used node at the tail. Every successful get moves the accessed node to the head. Every put either updates an existing node and moves it to the head, or creates a new node at the head. When the size exceeds capacity, remove the tail node from both the list and the map. Using a doubly linked list is essential because removal and insertion must both be O(1).',
      3 => 'Order of operations matters when the cache is full and you put a key that already exists. An existing key is an update, not an eviction, so the size does not grow and nothing is removed. A common bug is to evict before checking whether the key is already present, which incorrectly removes a different entry.',
      4 => 'The testing harness does not call your class directly. Instead, it plays a sequence of operations encoded as strings and records the outputs of the get calls. For example, the sequence put(1, 1), put(2, 2), get(1), put(3, 3), get(2) must produce the outputs 1 and -1, because inserting key 3 evicts key 2, which was the least recently used at that moment.',
    ),
    'examples' => 
    array (
      0 => 
      array (
        'input' => 'capacity = 2, operations = [put(1,1), put(2,2), get(1), put(3,3), get(2), put(4,4), get(1), get(3), get(4)]',
        'output' => '[1, -1, -1, 3, 4]',
        'explanation' => 'Key 2 is evicted when 3 is inserted; key 1 is evicted when 4 is inserted after being refreshed by get(1).',
      ),
      1 => 
      array (
        'input' => 'capacity = 1, operations = [put(1,10), put(2,20), get(1), get(2)]',
        'output' => '[-1, 20]',
        'explanation' => 'With capacity one, inserting key 2 evicts key 1 immediately.',
      ),
      2 => 
      array (
        'input' => 'capacity = 2, operations = [put(1,1), put(1,2), get(1)]',
        'output' => '[2]',
        'explanation' => 'Updating an existing key does not create a second entry and does not evict anything.',
      ),
    ),
    'constraints' => 
    array (
      0 => '1 <= capacity <= 3,000',
      1 => '0 <= key <= 10,000',
      2 => '0 <= value <= 100,000',
      3 => 'At most 200,000 operations are performed in total.',
      4 => 'Every get and put call must run in O(1) average time.',
    ),
    'follow_up' => 'How would you make the cache thread-safe? Can you extend the design so that entries also expire after a time-to-live?',
    'editorial' => 
    array (
      'insight' => 'A hash map finds a key in O(1), and a doubly linked list moves any node to the front in O(1). Use both structures together.',
      'steps' => 
      array (
        0 => 'Store a key to node mapping in a hash map.',
        1 => 'Keep a doubly linked list with the most recently used node at the head and the least recently used node at the tail.',
        2 => 'On get, return -1 when the key is missing. Otherwise move the node to the head and return its value.',
        3 => 'On put, update or insert the node at the head, then remove the tail node when the size exceeds capacity.',
      ),
      'time_complexity' => 'O(1) per operation',
      'space_complexity' => 'O(capacity)',
    ),
    'starter_code' => 
    array (
      'cpp' => '#include <unordered_map>
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
};',
    ),
    'testing' => 
    array (
      'judge' => 'The runner instantiates your class with the given capacity and replays the operation list in order. It collects every value returned by get and compares that list with the expected list. Constructors and put calls must not print anything.',
      'time_limit' => '3 seconds per test file',
      'memory_limit' => '256 MB',
      'languages' => 
      array (
        0 => 'C++ 20',
      ),
      'notes' => 
      array (
        0 => 'Operations are guaranteed to be valid; capacity and keys stay within the stated bounds.',
        1 => 'A get on a missing key returns -1 and does not change the recency order.',
        2 => 'A put on an existing key updates the value and refreshes its recency.',
        3 => 'Using a built-in ordered map such as std::map or a linear scan will time out on the largest tests.',
        4 => 'Node handles for an intrusive linked list must also be removed from the map when evicted.',
      ),
      'sample_tests' => 
      array (
        0 => 
        array (
          'name' => 'Classic eviction sequence',
          'input' => 'capacity = 2
put(1,1)
put(2,2)
get(1)
put(3,3)
get(2)',
          'expected' => '[1, -1]',
          'hidden' => false,
        ),
        1 => 
        array (
          'name' => 'Capacity of one',
          'input' => 'capacity = 1
put(1,10)
put(2,20)
get(1)
get(2)',
          'expected' => '[-1, 20]',
          'hidden' => false,
        ),
        2 => 
        array (
          'name' => 'Update without eviction',
          'input' => 'capacity = 2
put(1,1)
put(1,2)
get(1)',
          'expected' => '[2]',
          'hidden' => true,
        ),
        3 => 
        array (
          'name' => 'Recency refresh by get',
          'input' => 'capacity = 2
put(1,1)
put(2,2)
get(1)
put(3,3)
get(1)
get(2)',
          'expected' => '[1, 1, -1]',
          'hidden' => true,
        ),
      ),
    ),
    'hints' => 
    array (
      0 => 'You need O(1) lookup by key and O(1) access to the least recently used entry.',
      1 => 'A doubly linked list gives O(1) removal and insertion at both ends.',
      2 => 'Pointers stored in a hash map let you remove any node without scanning the list.',
      3 => 'Refresh the recency of a key on every successful get and on every put, including updates.',
      4 => 'Insert the new entry first and evict only after the size exceeds capacity, being careful not to evict the entry you just inserted.',
    ),
    'discipline' => 'DataStructure',
    'topic' => 'Cache Design',
    'companies' => 
    array (
      0 => 'Citadel',
      1 => 'Two Sigma',
      2 => '+3',
    ),
  ),
  4 => 
  array (
    'id' => 'course-schedule',
    'number' => 207,
    'title' => 'Course Schedule',
    'difficulty' => 'Medium',
    'acceptance_rate' => 47.6,
    'tags' => 
    array (
      0 => 'Graph',
      1 => 'Topological Sort',
      2 => 'DFS',
      3 => 'BFS',
    ),
    'summary' => 'Determine whether all courses can be finished given a list of prerequisites.',
    'description' => 
    array (
      0 => 'There are numCourses courses labelled from 0 to numCourses - 1. You are given a list of prerequisite pairs where each pair [a, b] means that course b must be completed before course a. Return true if it is possible to finish every course, and false otherwise.',
      1 => 'The prerequisites form a directed graph in which an edge points from b to a. Finishing all courses is possible exactly when this graph contains no directed cycle. If a cycle exists, every course on the cycle waits for another course on the same cycle, so none of them can ever be started.',
      2 => 'One way to detect a cycle is a depth-first search with three colours. Unvisited nodes are white, nodes on the current recursion path are grey, and fully processed nodes are black. If the search ever reaches a grey node, it has found a back edge and therefore a cycle. Marking a node black after all of its neighbours finish prevents repeated work and keeps the algorithm linear in the number of edges and nodes.',
      3 => 'An alternative and often simpler approach is Kahn algorithm for topological sorting. Compute the in-degree of every node, push all nodes with in-degree zero into a queue, and repeatedly remove a node, appending it to the order and decreasing the in-degree of its neighbours. If the final order contains every course, the graph is acyclic. If some courses remain unprocessed, those courses are part of or behind a cycle.',
      4 => 'Both approaches run in O(V + E) time and use O(V + E) memory, which fits comfortably within the limits. Be careful when the same prerequisite pair appears more than once, and remember that numCourses may be larger than the number of nodes mentioned in the prerequisites.',
    ),
    'examples' => 
    array (
      0 => 
      array (
        'input' => 'numCourses = 2, prerequisites = [[1,0]]',
        'output' => 'true',
        'explanation' => 'Course 0 must be taken first, then course 1. There is no cycle.',
      ),
      1 => 
      array (
        'input' => 'numCourses = 2, prerequisites = [[1,0],[0,1]]',
        'output' => 'false',
        'explanation' => 'Courses 0 and 1 depend on each other, so neither can start.',
      ),
      2 => 
      array (
        'input' => 'numCourses = 4, prerequisites = [[1,0],[2,1],[3,2]]',
        'output' => 'true',
        'explanation' => 'The courses can be taken in the order 0, 1, 2, 3.',
      ),
    ),
    'constraints' => 
    array (
      0 => '1 <= numCourses <= 2,000',
      1 => '0 <= prerequisites.length <= 5,000',
      2 => 'prerequisites[i].length == 2',
      3 => '0 <= a, b < numCourses',
      4 => 'All prerequisite pairs are unique.',
    ),
    'follow_up' => 'If the answer is true, can you return one valid order in which the courses can be taken? How does the solution change when prerequisites have weights or credits?',
    'editorial' => 
    array (
      'insight' => 'Every course can be finished exactly when the prerequisite graph contains no directed cycle.',
      'steps' => 
      array (
        0 => 'Build an adjacency list and compute the in-degree of every course.',
        1 => 'Push every course with in-degree zero into a queue.',
        2 => 'Pop a course, add it to the order, and decrement the in-degree of its neighbours.',
        3 => 'Push a neighbour when its in-degree reaches zero. Return true only when all courses are counted.',
      ),
      'time_complexity' => 'O(V + E)',
      'space_complexity' => 'O(V + E)',
    ),
    'starter_code' => 
    array (
      'cpp' => '#include <vector>
using namespace std;

class Solution {
public:
    bool canFinish(int numCourses, vector<vector<int>>& prerequisites) {
        // TODO: return true when no prerequisite cycle exists.
    }
};',
    ),
    'testing' => 
    array (
      'judge' => 'The runner reads the number of courses and the prerequisite list from standard input, then compares the first output token with the expected true or false. The comparison ignores surrounding whitespace only.',
      'time_limit' => '2 seconds per test file',
      'memory_limit' => '256 MB',
      'languages' => 
      array (
        0 => 'C++ 20',
      ),
      'notes' => 
      array (
        0 => 'The graph may be disconnected; every component must still be acyclic.',
        1 => 'Self-prerequisites such as [0,0] are possible in hidden tests and form an immediate cycle.',
        2 => 'A recursive DFS may exceed the default stack depth on a chain of 2,000 courses; an iterative DFS or BFS is safer.',
        3 => 'Prerequisite pairs are unique, so you do not need to deduplicate edges.',
      ),
      'sample_tests' => 
      array (
        0 => 
        array (
          'name' => 'Simple dependency',
          'input' => 'numCourses = 2, prerequisites = [[1,0]]',
          'expected' => 'true',
          'hidden' => false,
        ),
        1 => 
        array (
          'name' => 'Two-course cycle',
          'input' => 'numCourses = 2, prerequisites = [[1,0],[0,1]]',
          'expected' => 'false',
          'hidden' => false,
        ),
        2 => 
        array (
          'name' => 'Self loop',
          'input' => 'numCourses = 1, prerequisites = [[0,0]]',
          'expected' => 'false',
          'hidden' => true,
        ),
        3 => 
        array (
          'name' => 'Disconnected components',
          'input' => 'numCourses = 5, prerequisites = [[1,0],[2,1],[4,3]]',
          'expected' => 'true',
          'hidden' => true,
        ),
      ),
    ),
    'hints' => 
    array (
      0 => 'Model the prerequisites as a directed graph and look for a directed cycle.',
      1 => 'A DFS that tracks nodes on the current recursion path can detect a back edge.',
      2 => 'Kahn algorithm counts how many prerequisites each course still has pending.',
      3 => 'If the topological order contains fewer than numCourses nodes, a cycle was found.',
    ),
    'discipline' => 'Algorithms',
    'topic' => 'Graph Theory',
    'companies' => 
    array (
      0 => 'Optiver',
      1 => 'IMC',
    ),
  ),
  5 => 
  array (
    'id' => 'trapping-rain-water',
    'number' => 42,
    'title' => 'Trapping Rain Water',
    'difficulty' => 'Hard',
    'acceptance_rate' => 62.7,
    'tags' => 
    array (
      0 => 'Array',
      1 => 'Two Pointers',
      2 => 'Dynamic Programming',
      3 => 'Stack',
    ),
    'summary' => 'Compute how much rainwater is trapped between bars of varying heights.',
    'description' => 
    array (
      0 => 'You are given an array of non-negative integers representing an elevation map, where each value is the height of a bar with width one. After it rains, water collects in the valleys between taller bars. Return the total number of units of water that can be trapped.',
      1 => 'The amount of water above a single bar at index i is determined by the shorter of the tallest bar to its left and the tallest bar to its right, minus the height of the bar itself. If either side has no taller bar, no water can be held above that position. A direct implementation can precompute prefix maxima and suffix maxima, then sum the contribution of every index in O(n) time with O(n) extra space.',
      2 => 'The same idea can be implemented with two pointers and O(1) extra space. Place one pointer at each end of the array and maintain the maximum height seen from the left and from the right. Always advance the pointer whose current maximum is smaller, because that side is the limiting wall. The water contribution at that pointer is the current side maximum minus its height, clamped to zero. Every bar is visited once, so the running time stays O(n).',
      3 => 'A monotonic stack offers a third perspective: it keeps indices of bars in decreasing height order, and whenever a taller bar appears it closes a valley and computes the water held between the two walls. This approach is a useful test of understanding but is more error-prone than the two-pointer method, especially for equal heights.',
      4 => 'The total can exceed 32-bit integer range when the array is long and the heights are large, so use a 64-bit integer for the accumulator in C++ and Java. The algorithm should not modify the input array, and it must handle arrays with zero bars or bars of height zero.',
    ),
    'examples' => 
    array (
      0 => 
      array (
        'input' => 'height = [0,1,0,2,1,0,1,3,2,1,2,1]',
        'output' => '6',
        'explanation' => 'The elevation map traps six units of water in the valleys between the tall bars.',
      ),
      1 => 
      array (
        'input' => 'height = [4,2,0,3,2,5]',
        'output' => '9',
        'explanation' => 'Water is trapped above the bars of heights 2, 0, 3 and 2, and above parts of the surrounding slopes.',
      ),
      2 => 
      array (
        'input' => 'height = [1,0,1]',
        'output' => '1',
        'explanation' => 'The middle bar is lower than both neighbours, so it holds exactly one unit.',
      ),
    ),
    'constraints' => 
    array (
      0 => '1 <= height.length <= 20,000',
      1 => '0 <= height[i] <= 100,000',
      2 => 'The total answer fits in a signed 64-bit integer.',
    ),
    'follow_up' => 'Can you solve the problem in O(1) extra space? What changes if bars have width greater than one? Can you adapt the solution to return the maximum water trapped above any single bar?',
    'editorial' => 
    array (
      'insight' => 'The water above a bar is limited by the shorter of the tallest bars on its left and its right.',
      'steps' => 
      array (
        0 => 'Place one pointer at each end and track the maximum height seen from both sides.',
        1 => 'Advance the pointer whose side has the smaller maximum, because that side is the limiting wall.',
        2 => 'Add the side maximum minus the current height, clamped to zero, to the total.',
        3 => 'Update that side maximum and repeat until the pointers meet.',
      ),
      'time_complexity' => 'O(n)',
      'space_complexity' => 'O(1)',
    ),
    'starter_code' => 
    array (
      'cpp' => '#include <vector>
using namespace std;

class Solution {
public:
    long long trap(vector<int>& height) {
        // TODO: return the total units of trapped rainwater.
    }
};',
    ),
    'testing' => 
    array (
      'judge' => 'The runner reads one height array per test file and compares your first output token with the expected integer. Large tests verify that you use a wide enough integer type for the accumulator.',
      'time_limit' => '2 seconds per test file',
      'memory_limit' => '256 MB',
      'languages' => 
      array (
        0 => 'C++ 20',
      ),
      'notes' => 
      array (
        0 => 'The answer for the largest hidden test can exceed 2^31, so a 32-bit accumulator may overflow.',
        1 => 'Empty arrays and arrays with a single bar produce the answer 0.',
        2 => 'A quadratic solution that scans left and right for every bar will time out.',
        3 => 'Do not print the intermediate prefix or suffix arrays; only the final total is expected.',
      ),
      'sample_tests' => 
      array (
        0 => 
        array (
          'name' => 'Classic elevation map',
          'input' => 'height = [0,1,0,2,1,0,1,3,2,1,2,1]',
          'expected' => '6',
          'hidden' => false,
        ),
        1 => 
        array (
          'name' => 'Single valley',
          'input' => 'height = [1,0,1]',
          'expected' => '1',
          'hidden' => false,
        ),
        2 => 
        array (
          'name' => 'Monotonic slope',
          'input' => 'height = [1,2,3,4,5]',
          'expected' => '0',
          'hidden' => true,
        ),
        3 => 
        array (
          'name' => 'Wide basin',
          'input' => 'height = [100000,0,0,0,100000]',
          'expected' => '300000',
          'hidden' => true,
        ),
      ),
    ),
    'hints' => 
    array (
      0 => 'Water above a bar depends on the tallest bar on each side.',
      1 => 'Precomputing prefix and suffix maxima gives an O(n) solution immediately.',
      2 => 'To remove the extra arrays, advance the side with the smaller running maximum.',
      3 => 'The water contribution at the advanced pointer is its side maximum minus its own height.',
    ),
    'discipline' => 'Algorithms',
    'topic' => 'Two Pointers',
    'companies' => 
    array (
      0 => 'Jane Street',
      1 => 'HRT',
      2 => '+4',
    ),
  ),
  6 => 
  array (
    'id' => 'median-of-two-sorted-arrays',
    'number' => 4,
    'title' => 'Median of Two Sorted Arrays',
    'difficulty' => 'Hard',
    'acceptance_rate' => 42.4,
    'tags' => 
    array (
      0 => 'Array',
      1 => 'Binary Search',
      2 => 'Divide and Conquer',
    ),
    'summary' => 'Find the median of two sorted arrays in logarithmic time without merging them.',
    'description' => 
    array (
      0 => 'Given two sorted arrays nums1 and nums2 with sizes m and n, return the median of the combined multiset of values. The median is the middle value when the total number of elements is odd, and the average of the two middle values when the total is even. The combined array may have up to 2,000,000 elements.',
      1 => 'A merge-based solution is easy to write: walk both arrays with two pointers and stop at the middle positions. That costs O(m + n) time, which is linear and may be rejected by the strict time limit. The problem is designed to push for an O(log(min(m, n))) solution based on binary search over a partition of the shorter array.',
      2 => 'Imagine cutting the combined sorted sequence into a left half and a right half of roughly equal size. Each half is made of a prefix of nums1 and a prefix of nums2. If i elements come from nums1 and j elements come from nums2, then i + j must equal (m + n + 1) / 2 using integer division. The partition is correct when every element on the left is at most every element on the right, which reduces to two comparisons at the four boundary values.',
      3 => 'Binary search the value of i in the range 0 to m, always using the shorter array to keep the search space small. If the left value from nums1 is greater than the right value from nums2, the cut is too far right in nums1, so move it left. If the left value from nums2 is greater than the right value from nums1, the cut is too far left, so move it right. Otherwise the partition is valid and the median can be read directly from the boundary values.',
      4 => 'Treat out-of-range indices as negative and positive infinity, which keeps the comparison logic uniform and avoids separate branches for empty prefixes. Use doubles for the final average when the total length is even, and be careful with integer division and negative values. The constraints allow empty arrays, but not both at the same time.',
    ),
    'examples' => 
    array (
      0 => 
      array (
        'input' => 'nums1 = [1,3], nums2 = [2]',
        'output' => '2.00000',
        'explanation' => 'The merged array is [1,2,3] and its middle element is 2.',
      ),
      1 => 
      array (
        'input' => 'nums1 = [1,2], nums2 = [3,4]',
        'output' => '2.50000',
        'explanation' => 'The merged array is [1,2,3,4], and the average of 2 and 3 is 2.5.',
      ),
      2 => 
      array (
        'input' => 'nums1 = [], nums2 = [1]',
        'output' => '1.00000',
        'explanation' => 'An empty array contributes nothing, so the median is the only element of the second array.',
      ),
    ),
    'constraints' => 
    array (
      0 => '0 <= m <= 1,000,000',
      1 => '0 <= n <= 1,000,000',
      2 => '1 <= m + n <= 2,000,000',
      3 => '-1,000,000 <= nums1[i], nums2[i] <= 1,000,000',
      4 => 'Both input arrays are sorted in non-decreasing order.',
    ),
    'follow_up' => 'Can you find the k-th smallest element of the two arrays with the same partition idea? How would the approach extend to three sorted arrays?',
    'editorial' => 
    array (
      'insight' => 'The median is a partition: both halves contain the same number of elements, and every value on the left is at most every value on the right.',
      'steps' => 
      array (
        0 => 'Binary search the cut i in the shorter array.',
        1 => 'Derive j, the number of elements taken from the second array, from the required left-half size.',
        2 => 'If the left value from the first array exceeds the right value from the second, move i left. If the opposite happens, move i right.',
        3 => 'When the partition is valid, read the median from the four boundary values, treating missing boundaries as negative or positive infinity.',
      ),
      'time_complexity' => 'O(log(min(m, n)))',
      'space_complexity' => 'O(1)',
    ),
    'starter_code' => 
    array (
      'cpp' => '#include <vector>
using namespace std;

class Solution {
public:
    double findMedianSortedArrays(vector<int>& nums1, vector<int>& nums2) {
        // TODO: return the median of the combined sorted values.
    }
};',
    ),
    'testing' => 
    array (
      'judge' => 'The runner reads both arrays from standard input and compares your output with the expected median. Answers are accepted when the absolute or relative error is at most 1e-5, so printing five or more decimal places is safe.',
      'time_limit' => '2 seconds per test file',
      'memory_limit' => '512 MB',
      'languages' => 
      array (
        0 => 'C++ 20',
      ),
      'notes' => 
      array (
        0 => 'Print a floating point number with at least five digits after the decimal point.',
        1 => 'Both arrays may be empty individually, but never at the same time.',
        2 => 'The total length can reach two million, so allocating a merged array is expensive.',
        3 => 'A linear merge may pass small tests but will time out on the largest hidden ones.',
        4 => 'Duplicate values are common; compare with <= when validating the partition.',
      ),
      'sample_tests' => 
      array (
        0 => 
        array (
          'name' => 'Odd total length',
          'input' => 'nums1 = [1,3]
nums2 = [2]',
          'expected' => '2.00000',
          'hidden' => false,
        ),
        1 => 
        array (
          'name' => 'Even total length',
          'input' => 'nums1 = [1,2]
nums2 = [3,4]',
          'expected' => '2.50000',
          'hidden' => false,
        ),
        2 => 
        array (
          'name' => 'One empty array',
          'input' => 'nums1 = []
nums2 = [1]',
          'expected' => '1.00000',
          'hidden' => true,
        ),
        3 => 
        array (
          'name' => 'Negative values',
          'input' => 'nums1 = [-5,-3,-1]
nums2 = [-4,-2]',
          'expected' => '-2.50000',
          'hidden' => true,
        ),
      ),
    ),
    'hints' => 
    array (
      0 => 'The median splits the combined data into a left half and a right half of known sizes.',
      1 => 'If you choose how many elements come from the first array, the count from the second array is fixed.',
      2 => 'A valid cut has every left element less than or equal to every right element.',
      3 => 'Binary search the cut in the shorter array and treat missing boundaries as infinite.',
    ),
    'discipline' => 'Algorithms',
    'topic' => 'Binary Search',
    'companies' => 
    array (
      0 => 'CitSec',
      1 => 'HRT',
    ),
  ),
  7 => 
  array (
    'id' => 'word-ladder',
    'number' => 127,
    'title' => 'Word Ladder',
    'difficulty' => 'Hard',
    'acceptance_rate' => 41.2,
    'tags' => 
    array (
      0 => 'Hash Table',
      1 => 'String',
      2 => 'BFS',
    ),
    'summary' => 'Find the length of the shortest transformation sequence between two words.',
    'description' => 
    array (
      0 => 'A transformation sequence from beginWord to endWord is a list of words that starts with beginWord, ends with endWord, and in which every consecutive pair of words differs by exactly one letter. Every intermediate word must exist in the given wordList, and words may not be reused within the same sequence. Return the number of words in the shortest valid sequence, or 0 if no sequence exists.',
      1 => 'The search space is naturally a graph. Each word is a node, and an edge connects two words that differ in exactly one position. The shortest sequence is the shortest path from beginWord to endWord, so breadth-first search is the right tool. BFS explores words in increasing distance from the start, which means the first time endWord is reached the path is optimal.',
      2 => 'Naively generating all neighbours of a word costs O(L * 26) if you try every letter in every position, where L is the word length. Building the graph eagerly by comparing every pair of words costs O(N^2 * L), which is too slow when the list contains tens of thousands of words. The 26-letter trick is usually fast enough, and it avoids storing a huge adjacency list.',
      3 => 'A useful optimisation is to delete a word from the lookup set as soon as it is enqueued. Any later route that reaches the same word would be at least as long, so revisiting it cannot improve the answer. This keeps each word expanded once and turns the visited set and the dictionary into a single structure.',
      4 => 'The answer counts words rather than transitions, so the initial word contributes one to the level count. Handle the case where beginWord equals endWord by returning 1 immediately, and return 0 when endWord is not present in the word list. All words have the same length and consist of lowercase English letters only.',
    ),
    'examples' => 
    array (
      0 => 
      array (
        'input' => 'beginWord = "hit", endWord = "cog", wordList = ["hot","dot","dog","lot","log","cog"]',
        'output' => '5',
        'explanation' => 'One shortest sequence is hit -> hot -> dot -> dog -> cog, which contains five words.',
      ),
      1 => 
      array (
        'input' => 'beginWord = "hit", endWord = "cog", wordList = ["hot","dot","dog","lot","log"]',
        'output' => '0',
        'explanation' => 'The endWord is not in the list, so it can never be part of a transformation sequence.',
      ),
      2 => 
      array (
        'input' => 'beginWord = "a", endWord = "c", wordList = ["a","b","c"]',
        'output' => '2',
        'explanation' => 'The sequence a -> c is valid because the two words differ by exactly one letter, even though both are in the list.',
      ),
    ),
    'constraints' => 
    array (
      0 => '1 <= beginWord.length <= 10',
      1 => 'endWord.length == beginWord.length',
      2 => '1 <= wordList.length <= 10,000',
      3 => 'wordList[i].length == beginWord.length',
      4 => 'All words consist of lowercase English letters.',
      5 => 'All words in wordList are distinct.',
    ),
    'follow_up' => 'Can you return every shortest sequence instead of only its length? How would you speed up the search with bidirectional BFS?',
    'editorial' => 
    array (
      'insight' => 'Words are graph nodes and one-letter differences are edges, so the shortest transformation sequence is a breadth-first search layer count.',
      'steps' => 
      array (
        0 => 'Put the word list into a hash set and return 0 early when the end word is missing.',
        1 => 'Start BFS from the begin word with a distance of one.',
        2 => 'For each word, replace every position with each of the 26 letters to generate neighbours.',
        3 => 'Return the next distance when the end word is generated. Otherwise enqueue unseen neighbours and remove them from the set.',
      ),
      'time_complexity' => 'O(N * L * 26)',
      'space_complexity' => 'O(N * L)',
    ),
    'starter_code' => 
    array (
      'cpp' => '#include <string>
#include <vector>
using namespace std;

class Solution {
public:
    int ladderLength(string beginWord, string endWord, vector<string>& wordList) {
        // TODO: return the number of words in the shortest sequence, or 0.
    }
};',
    ),
    'testing' => 
    array (
      'judge' => 'The runner reads the begin word, the end word and the word list from standard input, then compares your first output token with the expected sequence length. An unreachable end word must be reported as 0.',
      'time_limit' => '3 seconds per test file',
      'memory_limit' => '256 MB',
      'languages' => 
      array (
        0 => 'C++ 20',
      ),
      'notes' => 
      array (
        0 => 'Words in the list are distinct, but beginWord may also appear in the list.',
        1 => 'A word may be used at most once in a sequence; the BFS visited set enforces this.',
        2 => 'Building a complete pairwise adjacency list will exceed the time limit on large inputs.',
        3 => 'The answer is the number of words in the path, not the number of one-letter edits.',
        4 => 'When endWord is absent from wordList the answer is always 0.',
      ),
      'sample_tests' => 
      array (
        0 => 
        array (
          'name' => 'Shortest ladder exists',
          'input' => 'beginWord = "hit"
endWord = "cog"
wordList = ["hot","dot","dog","lot","log","cog"]',
          'expected' => '5',
          'hidden' => false,
        ),
        1 => 
        array (
          'name' => 'Missing end word',
          'input' => 'beginWord = "hit"
endWord = "cog"
wordList = ["hot","dot","dog","lot","log"]',
          'expected' => '0',
          'hidden' => false,
        ),
        2 => 
        array (
          'name' => 'Single-letter words',
          'input' => 'beginWord = "a"
endWord = "c"
wordList = ["a","b","c"]',
          'expected' => '2',
          'hidden' => true,
        ),
        3 => 
        array (
          'name' => 'Long dead end',
          'input' => 'beginWord = "aaaa"
endWord = "zzzz"
wordList = ["aaab","aabb","abbb","bbbb","bbbz","bbzz","bzzz"]',
          'expected' => '0',
          'hidden' => true,
        ),
      ),
    ),
    'hints' => 
    array (
      0 => 'Words that differ by one letter are neighbours in a graph, and you need the shortest path.',
      1 => 'Breadth-first search visits nodes in order of increasing distance.',
      2 => 'Generate neighbours on the fly by replacing each position with 26 letters.',
      3 => 'Remove a word from the dictionary when you enqueue it so it is never expanded twice.',
    ),
    'discipline' => 'Algorithms',
    'topic' => 'BFS',
    'companies' => 
    array (
      0 => 'Google',
      1 => 'Uber',
    ),
  ),
  8 => 
  array (
    'id' => 'ring-buffer',
    'number' => 101,
    'title' => 'Ring Buffer',
    'difficulty' => 'Medium',
    'acceptance_rate' => 48.2,
    'tags' => 
    array (
      0 => 'Data structures',
      1 => 'Concurrency',
      2 => 'Memory',
    ),
    'summary' => 'Implement a fixed-capacity single-producer single-consumer ring buffer without allocations on the hot path.',
    'description' => 
    array (
      0 => 'You are building the transport buffer that sits between a market-data feed and the strategy engine. The producer writes packets at a steady rate, the consumer drains them in batches, and the buffer must never allocate after construction.',
      1 => 'Implement a bounded ring buffer with a fixed capacity supplied at construction time. Writes must fail cleanly when the buffer is full instead of overwriting unread data. Reads must return the oldest available element and preserve FIFO ordering.',
      2 => 'The judge instantiates the buffer on a single thread for the correctness suite, then exercises it with one producer and one consumer for the concurrency suite. Your implementation may use atomics, but it must not block the producer indefinitely.',
      3 => 'The capacity is a power of two in every test, so index masking is allowed. The buffer is destroyed only after both threads have joined, so you do not need to handle concurrent destruction.',
    ),
    'examples' => 
    array (
      0 => 
      array (
        'input' => 'RingBuffer buffer(4); buffer.push(1); buffer.push(2); int val; buffer.pop(val);',
        'output' => 'val == 1, buffer.size() == 1',
        'explanation' => 'Elements are returned in strict FIFO order.',
      ),
      1 => 
      array (
        'input' => 'RingBuffer buffer(2); buffer.push(10); buffer.push(20); bool ok = buffer.push(30);',
        'output' => 'ok == false, buffer.size() == 2',
        'explanation' => 'Writes fail cleanly when the buffer reaches capacity.',
      ),
    ),
    'constraints' => 
    array (
      0 => '1 <= capacity <= 1,048,576 and capacity is always a power of two.',
      1 => 'No heap allocation is permitted after construction.',
      2 => 'push and pop must be safe with exactly one producer thread and one consumer thread.',
      3 => 'T is trivially copyable in the concurrency suite and may be movable in the correctness suite.',
      4 => 'The implementation must compile under C++20 with -Wall -Wextra -Werror.',
    ),
    'follow_up' => 'Can you keep the producer and consumer counters on separate cache lines to eliminate false sharing?',
    'editorial' => 
    array (
      'insight' => 'A ring buffer without a shared size counter is the standard way to keep producer and consumer off the same cache line.',
      'steps' => 
      array (
        0 => 'Allocate storage once and keep the raw capacity.',
        1 => 'Store the read and write counters on separate cache lines.',
        2 => 'Release-store the element, then release-store the advanced index.',
        3 => 'Acquire-load the opposite index before dereferencing storage.',
        4 => 'Reject pushes when write - read equals capacity.',
      ),
      'time_complexity' => 'O(1) per operation',
      'space_complexity' => 'O(capacity)',
    ),
    'starter_code' => 
    array (
      'cpp' => '#include <atomic>
#include <cstddef>
#include <vector>

namespace arena {

template <typename T>
class RingBuffer {
public:
    explicit RingBuffer(std::size_t capacity)
        : storage_(capacity), capacity_(capacity) {}

    bool push(const T& value) {
        // TODO: publish the value and advance the write index.
        (void)value;
        return false;
    }

    bool pop(T& out) {
        // TODO: consume the oldest value and advance the read index.
        (void)out;
        return false;
    }

    std::size_t size() const noexcept {
        return write_.load(std::memory_order_acquire) -
               read_.load(std::memory_order_acquire);
    }

private:
    std::vector<T> storage_;
    std::size_t capacity_;
    std::atomic<std::size_t> write_{0};
    std::atomic<std::size_t> read_{0};
};

}  // namespace arena',
    ),
    'testing' => 
    array (
      'judge' => 'Your implementation is compiled under C++20 with -O3 -Wall -Wextra -Werror. The correctness suite tests sequential operations, boundary conditions, and capacity limits. The concurrency suite runs a producer thread and a consumer thread with ThreadSanitizer enabled.',
      'time_limit' => '2 seconds per test file',
      'memory_limit' => '256 MB',
      'languages' => 
      array (
        0 => 'C++ 20',
      ),
      'notes' => 
      array (
        0 => 'No heap allocation is permitted after construction.',
        1 => 'push and pop must be safe with exactly one producer thread and one consumer thread.',
        2 => 'Index masking using capacity - 1 is allowed because capacity is guaranteed to be a power of two.',
      ),
      'sample_tests' => 
      array (
        0 => 
        array (
          'name' => 'FIFO ordering',
          'input' => 'push 1..8, pop 8',
          'expected' => '1 2 3 4 5 6 7 8',
          'hidden' => false,
        ),
        1 => 
        array (
          'name' => 'Full buffer rejects writes',
          'input' => 'capacity=4, push 1..5',
          'expected' => '4 accepted, 1 rejected',
          'hidden' => false,
        ),
        2 => 
        array (
          'name' => 'Wrap-around',
          'input' => 'capacity=4, interleaved push/pop',
          'expected' => 'FIFO order preserved',
          'hidden' => false,
        ),
        3 => 
        array (
          'name' => 'Single producer / single consumer',
          'input' => '10M packets across two threads',
          'expected' => 'no loss, no reordering',
          'hidden' => true,
        ),
      ),
    ),
    'hints' => 
    array (
      0 => 'Keep the read and write indices monotonically increasing and mask only when indexing into storage.',
      1 => 'The producer owns the write index and the consumer owns the read index. Each side only needs an acquire load of the other side.',
      2 => 'Publish the value before the index update with a release store.',
      3 => 'A full buffer is detected with write - read == capacity. Do not add a separate size counter.',
    ),
    'discipline' => 'Concurrency',
    'topic' => 'Lock-Free Ring',
    'companies' => 
    array (
      0 => 'Optiver',
      1 => 'IMC',
      2 => '+8',
    ),
  ),
  9 => 
  array (
    'id' => 'lock-free-stack',
    'number' => 102,
    'title' => 'Lock-Free Stack',
    'difficulty' => 'Hard',
    'acceptance_rate' => 31.5,
    'tags' => 
    array (
      0 => 'Concurrency',
      1 => 'Atomics',
      2 => 'Lifetime',
    ),
    'summary' => 'Build a Treiber stack with safe memory reclamation and prove it survives concurrent push and pop.',
    'description' => 
    array (
      0 => 'Implement a lock-free stack that supports concurrent push and pop from any number of threads. The algorithm must not use a global mutex and must not leak nodes when a pop fails.',
      1 => 'The hard part is memory reclamation. A thread may read a node pointer, be descheduled, and wake up after another thread has popped and freed that node. The provided harness uses hazard pointers, and you must integrate them rather than inventing a new reclamation scheme.',
      2 => 'The grader runs a stress test with four producers and four consumers for five seconds, then checks that every pushed value is popped exactly once. A leak check runs at the end with ASan and LSan enabled.',
    ),
    'examples' => 
    array (
      0 => 
      array (
        'input' => 'LockFreeStack stack; stack.push(10); stack.push(20); int val; stack.pop(val);',
        'output' => 'val == 20',
        'explanation' => 'LIFO order is preserved.',
      ),
      1 => 
      array (
        'input' => 'LockFreeStack stack; int val; bool ok = stack.pop(val);',
        'output' => 'ok == false',
        'explanation' => 'Popping from an empty stack returns false.',
      ),
    ),
    'constraints' => 
    array (
      0 => '1 <= number of threads <= 16.',
      1 => 'No mutex, condition variable, or blocking primitive may be used.',
      2 => 'Every pushed value must be popped exactly once.',
      3 => 'No leaks under LeakSanitizer after all nodes are popped.',
      4 => 'Compiles with -std=c++20 -Wall -Wextra -Werror.',
    ),
    'follow_up' => 'How does hazard-pointer reclamation compare with epoch-based reclamation in read-heavy workloads?',
    'editorial' => 
    array (
      'insight' => 'Hazard pointers let a reader announce which node it is about to touch, so a writer can defer reclamation.',
      'steps' => 
      array (
        0 => 'Push allocates a node and CAS-loops it onto the head.',
        1 => 'Pop protects the current head, re-reads it, and retries on change.',
        2 => 'After the head is protected, read the value and CAS the head to node->next.',
        3 => 'Retire the unlinked node instead of deleting it immediately.',
        4 => 'Reclaim retired nodes once no hazard pointer references them.',
      ),
      'time_complexity' => 'O(1) expected per operation',
      'space_complexity' => 'O(threads + retired nodes)',
    ),
    'starter_code' => 
    array (
      'cpp' => '#include <atomic>

namespace arena {

class HazardPointers;

template <typename T>
class LockFreeStack {
    struct Node {
        T value;
        Node* next;
    };

public:
    explicit LockFreeStack(HazardPointers& hazard) : hazard_(&hazard) {}

    void push(T value) {
        // TODO: link a new node with a release store.
        (void)value;
    }

    bool pop(T& out) {
        // TODO: protect head, read value, unlink safely.
        (void)out;
        return false;
    }

    bool empty() const noexcept {
        return head_.load(std::memory_order_acquire) == nullptr;
    }

private:
    std::atomic<Node*> head_{nullptr};
    HazardPointers* hazard_;
};

}  // namespace arena',
    ),
    'testing' => 
    array (
      'judge' => 'The harness tests sequential correctness, then launches concurrent worker threads with AddressSanitizer and ThreadSanitizer enabled.',
      'time_limit' => '5 seconds',
      'memory_limit' => '512 MB',
      'languages' => 
      array (
        0 => 'C++ 20',
      ),
      'notes' => 
      array (
        0 => 'No mutex or lock-based primitives permitted.',
        1 => 'Must be clean under LSan and TSan.',
      ),
      'sample_tests' => 
      array (
        0 => 
        array (
          'name' => 'Single-thread push/pop',
          'input' => 'push 1..1000, pop all',
          'expected' => 'LIFO order',
          'hidden' => false,
        ),
        1 => 
        array (
          'name' => 'Concurrent producers',
          'input' => '4 threads push 250k values',
          'expected' => 'all values present',
          'hidden' => false,
        ),
        2 => 
        array (
          'name' => 'Leak check',
          'input' => 'drain stack, run LSan',
          'expected' => '0 leaks',
          'hidden' => true,
        ),
      ),
    ),
    'hints' => 
    array (
      0 => 'A Treiber stack is a singly linked list with an atomic head.',
      1 => 'Protect the head before dereferencing.',
      2 => 'Unlink first, then retire node.',
    ),
    'discipline' => 'Concurrency',
    'topic' => 'Hazard Pointers',
    'companies' => 
    array (
      0 => 'CitSec',
      1 => 'CTC',
      2 => '+2',
    ),
  ),
  10 => 
  array (
    'id' => 'dangling-reference',
    'number' => 103,
    'title' => 'Dangling Reference',
    'difficulty' => 'Medium',
    'acceptance_rate' => 62.7,
    'tags' => 
    array (
      0 => 'Lifetime',
      1 => 'References',
      2 => 'Debugging',
    ),
    'summary' => 'Find and fix the lifetime bug in a configuration loader that returns views into a temporary buffer.',
    'description' => 
    array (
      0 => 'The configuration loader parses a key-value file into a map and returns string_views pointing into the file buffer. The buffer is a local std::string that is destroyed when the function returns, so every returned view dangles.',
      1 => 'Your job is to fix the lifetime issue without changing the public API of the loader. The returned views must remain valid for as long as the returned configuration object is alive.',
    ),
    'examples' => 
    array (
      0 => 
      array (
        'input' => 'load_config("app.conf")',
        'output' => 'config.values["host"] == "localhost"',
        'explanation' => 'Views point to valid memory for the life of the Config.',
      ),
    ),
    'constraints' => 
    array (
      0 => 'Public signature of load_config must not change.',
      1 => 'Config must be copyable and movable.',
      2 => 'Must be clean under ASan.',
    ),
    'follow_up' => 'How can you prevent dangling views when Config is moved to a new memory location?',
    'editorial' => 
    array (
      'insight' => 'Config must own the underlying buffer so views point to storage tied to the object lifetime.',
      'steps' => 
      array (
        0 => 'Add owning storage to Config.',
        1 => 'Populate views against the permanent buffer.',
        2 => 'Verify move and copy semantics under ASan.',
      ),
      'time_complexity' => 'O(n)',
      'space_complexity' => 'O(n)',
    ),
    'starter_code' => 
    array (
      'cpp' => '#include <filesystem>
#include <string>
#include <string_view>
#include <unordered_map>

namespace arena {

struct Config {
    std::unordered_map<std::string, std::string_view> values;
    std::string backing;
};

Config load_config(const std::filesystem::path& path) {
    // TODO: load file and return Config without dangling views.
    return {};
}

}  // namespace arena',
    ),
    'testing' => 
    array (
      'judge' => 'Compiled with AddressSanitizer. Reads from returned string_views after function returns must be clean.',
      'time_limit' => '2 seconds',
      'memory_limit' => '256 MB',
      'languages' => 
      array (
        0 => 'C++ 20',
      ),
      'notes' => 
      array (
        0 => 'Clean under ASan on files > 1 MB.',
      ),
      'sample_tests' => 
      array (
        0 => 
        array (
          'name' => 'Small config',
          'input' => 'key=value',
          'expected' => 'value readable',
          'hidden' => false,
        ),
        1 => 
        array (
          'name' => 'Large config',
          'input' => '1 MB file',
          'expected' => 'clean under ASan',
          'hidden' => false,
        ),
      ),
    ),
    'hints' => 
    array (
      0 => 'string_view is non-owning.',
      1 => 'Store buffer inside Config.',
    ),
    'discipline' => 'Systems',
    'topic' => 'Lifetime & Views',
    'companies' => 
    array (
      0 => 'Cloudflare',
      1 => 'Fastly',
    ),
  ),
  11 => 
  array (
    'id' => 'hot-loop-optimization',
    'number' => 104,
    'title' => 'Hot Loop Optimization',
    'difficulty' => 'Hard',
    'acceptance_rate' => 38.9,
    'tags' => 
    array (
      0 => 'Performance',
      1 => 'Cache',
      2 => 'SIMD',
    ),
    'summary' => 'Make a histogram kernel three times faster without changing its observable output.',
    'description' => 
    array (
      0 => 'The provided histogram kernel processes one billion bytes and is bandwidth-bound on paper, but benchmarks show it running at less than a third of available memory bandwidth. Your task is to close that gap.',
      1 => 'A submission is accepted when the median of five runs is at least 3x faster than the reference and the output matches exactly.',
    ),
    'examples' => 
    array (
      0 => 
      array (
        'input' => 'data = [66, 66, ...], size = 1000',
        'output' => 'bins[66] == 1000',
        'explanation' => 'Frequencies match reference.',
      ),
    ),
    'constraints' => 
    array (
      0 => 'data is 64-byte aligned and size is multiple of 64.',
      1 => 'Output must match reference exactly for all 256 bins.',
      2 => 'Single core only.',
    ),
    'follow_up' => 'Can multi-replica histogramming avoid cache conflicts?',
    'editorial' => 
    array (
      'insight' => 'Replicating histogram bins removes loop dependency chains.',
      'steps' => 
      array (
        0 => 'Create 4 cacheline-aligned bin replicas.',
        1 => 'Unroll the main loop by 16 or 32 bytes.',
        2 => 'Merge replicas into final output array.',
      ),
      'time_complexity' => 'O(n + 256)',
      'space_complexity' => 'O(1)',
    ),
    'starter_code' => 
    array (
      'cpp' => '#include <cstddef>
#include <cstdint>

namespace arena {

void histogram(const std::uint8_t* data, std::size_t size, std::uint64_t bins[256]) {
    // TODO: implement 3x faster histogram kernel.
    for (std::size_t i = 0; i < size; ++i) {
        ++bins[data[i]];
    }
}

}  // namespace arena',
    ),
    'testing' => 
    array (
      'judge' => 'Compiled with -O3. Runs median of 5 benchmark iterations on 1 GB buffer.',
      'time_limit' => '3 seconds',
      'memory_limit' => '256 MB',
      'languages' => 
      array (
        0 => 'C++ 20',
      ),
      'notes' => 
      array (
        0 => 'Benchmark must achieve >= 3x speedup.',
      ),
      'sample_tests' => 
      array (
        0 => 
        array (
          'name' => 'Uniform data',
          'input' => '16 MB random',
          'expected' => 'matches reference',
          'hidden' => false,
        ),
        1 => 
        array (
          'name' => 'Benchmark',
          'input' => '1 GB',
          'expected' => '>= 3x reference',
          'hidden' => true,
        ),
      ),
    ),
    'hints' => 
    array (
      0 => 'Break store-to-load dependencies with multiple replica bins.',
      1 => 'Unroll the inner loop.',
    ),
    'discipline' => 'Performance',
    'topic' => 'Cache & SIMD',
    'companies' => 
    array (
      0 => 'Two Sigma',
      1 => 'Jump Trading',
    ),
  ),
  12 => 
  array (
    'id' => 'concept-constrained-container',
    'number' => 105,
    'title' => 'Concept-Constrained Container',
    'difficulty' => 'Medium',
    'acceptance_rate' => 55.1,
    'tags' => 
    array (
      0 => 'Templates',
      1 => 'Concepts',
      2 => 'STL',
    ),
    'summary' => 'Design a small-vector type constrained by C++20 concepts, with correct iterator and allocator semantics.',
    'description' => 
    array (
      0 => 'Implement a fixed-capacity inline vector that stores up to N elements without allocating. The type must satisfy the vector-like API and must be constrained with C++20 concepts.',
      1 => 'push_back must be noexcept when the element type is nothrow move constructible. Destruction must be correct for non-trivial types.',
    ),
    'examples' => 
    array (
      0 => 
      array (
        'input' => 'InlineVector<int, 4> v; v.push_back(10); v.push_back(20);',
        'output' => 'v.size() == 2, v[0] == 10',
        'explanation' => 'Inline vector works without heap allocation.',
      ),
    ),
    'constraints' => 
    array (
      0 => 'N >= 1 and compile-time constant.',
      1 => 'No dynamic allocation.',
      2 => 'Supports move-only element types.',
    ),
    'follow_up' => 'Can you implement constexpr construction and destruction?',
    'editorial' => 
    array (
      'insight' => 'Use std::construct_at and std::destroy_at with aligned byte storage.',
      'steps' => 
      array (
        0 => 'Allocate aligned byte buffer.',
        1 => 'Construct elements with std::construct_at.',
        2 => 'Destroy live elements in destructor.',
      ),
      'time_complexity' => 'O(1) per operation',
      'space_complexity' => 'O(N)',
    ),
    'starter_code' => 
    array (
      'cpp' => '#include <concepts>
#include <cstddef>
#include <utility>

namespace arena {

template <typename T, std::size_t N>
    requires std::movable<T> && std::destructible<T>
class InlineVector {
public:
    InlineVector() noexcept = default;

    bool push_back(const T& value) {
        // TODO: construct in place when capacity remains.
        (void)value;
        return false;
    }

    bool push_back(T&& value) {
        // TODO: move-construct in place when capacity remains.
        (void)value;
        return false;
    }

    std::size_t size() const noexcept { return size_; }

private:
    alignas(T) unsigned char storage_[sizeof(T) * N];
    std::size_t size_ = 0;
};

}  // namespace arena',
    ),
    'testing' => 
    array (
      'judge' => 'Tested with move-only, throwing move, and non-trivial destructor types.',
      'time_limit' => '2 seconds',
      'memory_limit' => '256 MB',
      'languages' => 
      array (
        0 => 'C++ 20',
      ),
      'notes' => 
      array (
        0 => 'Concept requirements must reject non-movable types with a clear diagnostic.',
      ),
      'sample_tests' => 
      array (
        0 => 
        array (
          'name' => 'Basic push',
          'input' => 'push 3 ints',
          'expected' => 'size == 3',
          'hidden' => false,
        ),
      ),
    ),
    'hints' => 
    array (
      0 => 'Use std::construct_at and std::destroy_at.',
      1 => 'Track constructed count accurately.',
    ),
    'discipline' => 'LanguageKnowledge',
    'topic' => 'C++20 Concepts',
    'companies' => 
    array (
      0 => 'Epic Games',
      1 => 'Valve',
    ),
  ),
  13 => 
  array (
    'id' => 'span-overflow',
    'number' => 106,
    'title' => 'Span Overflow',
    'difficulty' => 'Easy',
    'acceptance_rate' => 71.4,
    'tags' => 
    array (
      0 => 'Spans',
      1 => 'Bounds',
      2 => 'Debugging',
    ),
    'summary' => 'Fix an off-by-one bounds check in a packet decoder that reads one byte past a span.',
    'description' => 
    array (
      0 => 'A packet decoder reads a header length, then copies that many bytes into a fixed output buffer. The bounds check uses a strict less-than comparison, so a packet whose payload ends exactly at the end of the span is rejected, while a packet with a one-byte overflow slips through.',
      1 => 'Fix the decoder so that it accepts every well-formed packet and rejects every malformed one.',
    ),
    'examples' => 
    array (
      0 => 
      array (
        'input' => 'packet = [0x02, 0x00, 0xAA, 0xBB]',
        'output' => 'decode returns true',
        'explanation' => 'Valid payload is decoded cleanly.',
      ),
    ),
    'constraints' => 
    array (
      0 => 'packet.size() may be zero.',
      1 => 'payload_length is little-endian.',
      2 => 'No reads beyond packet.size() under ASan.',
    ),
    'follow_up' => 'How can std::span::subspan avoid manual pointer arithmetic?',
    'editorial' => 
    array (
      'insight' => 'Promote length to size_t and check packet.size() - sizeof(Header) >= payload_length.',
      'steps' => 
      array (
        0 => 'Validate minimum header size.',
        1 => 'Compute payload bounds in size_t.',
        2 => 'Copy payload to output buffer.',
      ),
      'time_complexity' => 'O(payload)',
      'space_complexity' => 'O(1)',
    ),
    'starter_code' => 
    array (
      'cpp' => '#include <cstddef>
#include <cstdint>
#include <cstring>
#include <span>

namespace arena {

struct Header {
    std::uint16_t payload_length;
};

bool decode(std::span<const std::byte> packet, std::span<std::byte> out) {
    if (packet.size() < sizeof(Header)) {
        return false;
    }
    Header header;
    std::memcpy(&header, packet.data(), sizeof(header));
    if (packet.size() < sizeof(Header) + header.payload_length) {
        return false;
    }
    if (out.size() < header.payload_length) {
        return false;
    }
    std::memcpy(out.data(), packet.data() + sizeof(Header), header.payload_length);
    return true;
}

}  // namespace arena',
    ),
    'testing' => 
    array (
      'judge' => 'Tested with AddressSanitizer across fuzz corpus including empty, boundary, and overflow packets.',
      'time_limit' => '2 seconds',
      'memory_limit' => '256 MB',
      'languages' => 
      array (
        0 => 'C++ 20',
      ),
      'notes' => 
      array (
        0 => 'Clean under ASan.',
      ),
      'sample_tests' => 
      array (
        0 => 
        array (
          'name' => 'Valid packet',
          'input' => '4 byte payload',
          'expected' => 'decoded',
          'hidden' => false,
        ),
      ),
    ),
    'hints' => 
    array (
      0 => 'Promote arithmetic to size_t to prevent 16-bit integer overflow.',
    ),
    'discipline' => 'Networking',
    'topic' => 'Packet Decoder',
    'companies' => 
    array (
      0 => 'Cisco',
      1 => 'Arista',
    ),
  ),
  14 => 
  array (
    'id' => 'small-string-optimization',
    'number' => 107,
    'title' => 'Small String Optimization',
    'difficulty' => 'Hard',
    'acceptance_rate' => 27.8,
    'tags' => 
    array (
      0 => 'Memory',
      1 => 'Strings',
      2 => 'ABI',
    ),
    'summary' => 'Shrink a hot string type from 32 to 24 bytes while preserving SSO and all documented behaviour.',
    'description' => 
    array (
      0 => 'The engine stores millions of short identifiers in a custom string type. The current layout is 32 bytes, which wastes a cache line on every two identifiers. Your task is to reduce the object size to 24 bytes without changing observable behaviour.',
      1 => 'The type must keep its small-string optimisation for strings up to 15 characters, support move-only ownership, and remain valid after copy and move.',
    ),
    'examples' => 
    array (
      0 => 
      array (
        'input' => 'SmallString s("hello");',
        'output' => 's.is_small() == true, sizeof(s) == 24',
        'explanation' => 'String is stored inline without heap allocation.',
      ),
    ),
    'constraints' => 
    array (
      0 => 'sizeof(SmallString) == 24.',
      1 => 'Strings <= 15 chars must not allocate.',
      2 => 'Clean under ASan and UBSan.',
    ),
    'follow_up' => 'How does folly::FBString pack 23 characters into 24 bytes?',
    'editorial' => 
    array (
      'insight' => 'Overlap capacity with the inline buffer and use a bit in the size field as the SSO flag.',
      'steps' => 
      array (
        0 => 'Define a union of inline buffer and heap pointer/capacity.',
        1 => 'Use tagged size representation.',
        2 => 'Dispatch destructor based on the tag bit.',
      ),
      'time_complexity' => 'O(1) operations, O(n) copy',
      'space_complexity' => '24 bytes per string',
    ),
    'starter_code' => 
    array (
      'cpp' => '#include <cstddef>
#include <string>
#include <string_view>

namespace arena {

class SmallString {
public:
    SmallString() = default;
    explicit SmallString(std::string_view value) : heap_(value) {}
    std::string_view view() const noexcept { return heap_; }

private:
    std::string heap_;
};

static_assert(sizeof(SmallString) == 24 || true);

}  // namespace arena',
    ),
    'testing' => 
    array (
      'judge' => 'Checks static_assert on sizeof, verifies allocations with custom allocator hook, and benchmarks allocation rate.',
      'time_limit' => '3 seconds',
      'memory_limit' => '256 MB',
      'languages' => 
      array (
        0 => 'C++ 20',
      ),
      'notes' => 
      array (
        0 => 'sizeof(SmallString) must be exactly 24 bytes.',
      ),
      'sample_tests' => 
      array (
        0 => 
        array (
          'name' => 'Small string',
          'input' => '10 chars',
          'expected' => '0 heap allocations',
          'hidden' => false,
        ),
      ),
    ),
    'hints' => 
    array (
      0 => 'Store the small flag in the highest bit of the size field.',
    ),
    'discipline' => 'Systems',
    'topic' => 'Memory Layout',
    'companies' => 
    array (
      0 => 'Bloomberg',
      1 => 'Meta',
    ),
  ),
  15 => 
  array (
    'id' => 'move-semantics-audit',
    'number' => 108,
    'title' => 'Move Semantics Audit',
    'difficulty' => 'Easy',
    'acceptance_rate' => 84.6,
    'tags' => 
    array (
      0 => 'Move semantics',
      1 => 'Value categories',
    ),
    'summary' => 'Answer a short quiz about value categories, moved-from state, and noexcept move constructors.',
    'description' => 
    array (
      0 => 'This audit checks whether you can reason about value categories and moved-from objects without running code.',
      1 => 'The questions cover the difference between std::move and an actual move, the state of a moved-from standard-library object, and the effect of a throwing move constructor on container reallocation.',
    ),
    'examples' => 
    array (
      0 => 
      array (
        'input' => 'std::string s = "test"; std::move(s);',
        'output' => 's remains an lvalue; std::move(s) is an xvalue',
        'explanation' => 'std::move is an unconditional cast to an rvalue reference.',
      ),
    ),
    'constraints' => 
    array (
      0 => 'Identify value categories accurately.',
      1 => 'Reason about valid but unspecified moved-from states.',
    ),
    'follow_up' => 'Why does std::vector require noexcept move constructors for the strong exception guarantee?',
    'editorial' => 
    array (
      'insight' => 'Value categories describe expressions, not types. A named rvalue reference is itself an lvalue.',
      'steps' => 
      array (
        0 => 'Distinguish lvalue, prvalue, and xvalue expressions.',
        1 => 'Remember that std::move does not move anything on its own.',
        2 => 'Mark move constructors noexcept whenever possible.',
      ),
      'time_complexity' => 'O(1)',
      'space_complexity' => 'O(1)',
    ),
    'starter_code' => 
    array (
      'cpp' => 'namespace arena {

struct MoveAudit {
    static constexpr bool is_move_just_a_cast = true;
    static constexpr bool moved_from_is_valid_unspecified = true;
};

}  // namespace arena',
    ),
    'testing' => 
    array (
      'judge' => 'Verifies answers to value category and move semantics questions.',
      'time_limit' => '1 second',
      'memory_limit' => '128 MB',
      'languages' => 
      array (
        0 => 'C++ 20',
      ),
      'notes' => 
      array (
        0 => 'All questions must be answered correctly.',
      ),
      'sample_tests' => 
      array (
        0 => 
        array (
          'name' => 'Value category check',
          'input' => 'std::move(x)',
          'expected' => 'xvalue',
          'hidden' => false,
        ),
      ),
    ),
    'hints' => 
    array (
      0 => 'std::move does not move; it casts.',
    ),
    'discipline' => 'LanguageKnowledge',
    'topic' => 'Value Categories',
    'companies' => 
    array (
      0 => 'Akuna',
      1 => 'Wolverine',
    ),
  ),
  16 => 
  array (
    'id' => 'thread-pool',
    'number' => 109,
    'title' => 'Thread Pool',
    'difficulty' => 'Hard',
    'acceptance_rate' => 34.2,
    'tags' => 
    array (
      0 => 'Concurrency',
      1 => 'Threads',
      2 => 'Lifetime',
    ),
    'summary' => 'Implement a work-stealing thread pool with graceful shutdown and exception propagation.',
    'description' => 
    array (
      0 => 'Implement a thread pool that accepts callables, executes them on a fixed number of workers, and returns a future for each task. When the pool is destroyed it must finish queued work and join every worker.',
      1 => 'Tasks may submit other tasks. The pool must make nested submission safe. If a task throws, the exception must be stored in its future rather than terminating the process.',
    ),
    'examples' => 
    array (
      0 => 
      array (
        'input' => 'ThreadPool pool(4); auto f = pool.submit([] { return 42; });',
        'output' => 'f.get() == 42',
        'explanation' => 'Task executes on background thread and result is delivered via std::future.',
      ),
    ),
    'constraints' => 
    array (
      0 => '1 <= workers <= std::thread::hardware_concurrency().',
      1 => 'Nested submission must not deadlock.',
      2 => 'Exceptions must be delivered through std::future.',
      3 => 'Clean under TSan and ASan.',
    ),
    'follow_up' => 'How would you implement work-stealing with per-worker deques?',
    'editorial' => 
    array (
      'insight' => 'A thread pool is a task queue, worker threads, and a clean shutdown protocol.',
      'steps' => 
      array (
        0 => 'Wrap each callable in a packaged_task.',
        1 => 'Signal workers when a task is queued and when shutdown begins.',
        2 => 'Workers pop tasks under the mutex and run them outside the lock.',
        3 => 'Join every worker on destruction.',
      ),
      'time_complexity' => 'O(1) per submission',
      'space_complexity' => 'O(workers + queued tasks)',
    ),
    'starter_code' => 
    array (
      'cpp' => '#include <cstddef>
#include <functional>
#include <future>
#include <queue>
#include <thread>
#include <vector>

namespace arena {

class ThreadPool {
public:
    explicit ThreadPool(std::size_t workers) {
        // TODO: start workers and initialize queue.
        (void)workers;
    }

    ~ThreadPool() {
        // TODO: finish work and join workers.
    }

    template <typename F>
    auto submit(F&& task) -> std::future<std::invoke_result_t<F>> {
        using Result = std::invoke_result_t<F>;
        auto job = std::make_shared<std::packaged_task<Result()>>(std::forward<F>(task));
        auto future = job->get_future();
        // TODO: enqueue job
        return future;
    }

private:
    std::vector<std::thread> workers_;
};

}  // namespace arena',
    ),
    'testing' => 
    array (
      'judge' => 'Tested with ThreadSanitizer. Validates throughput, nested submissions, and exception propagation.',
      'time_limit' => '5 seconds',
      'memory_limit' => '512 MB',
      'languages' => 
      array (
        0 => 'C++ 20',
      ),
      'notes' => 
      array (
        0 => 'Clean under TSan and ASan.',
      ),
      'sample_tests' => 
      array (
        0 => 
        array (
          'name' => '1000 tasks',
          'input' => 'submit 1000 counter tasks',
          'expected' => 'all complete',
          'hidden' => false,
        ),
      ),
    ),
    'hints' => 
    array (
      0 => 'Use packaged_task wrapped in shared_ptr.',
      1 => 'Condition variable wake-up on shutdown.',
    ),
    'discipline' => 'Concurrency',
    'topic' => 'Work Stealing',
    'companies' => 
    array (
      0 => 'Jane Street',
      1 => 'QRT',
      2 => '+3',
    ),
  ),
  17 => 
  array (
    'id' => 'constexpr-matrix',
    'number' => 110,
    'title' => 'Constexpr Matrix',
    'difficulty' => 'Medium',
    'acceptance_rate' => 58.3,
    'tags' => 
    array (
      0 => 'constexpr',
      1 => 'Numerics',
      2 => 'Templates',
    ),
    'summary' => 'Evaluate a fixed-size matrix multiply at compile time and prove the result is available in a static_assert.',
    'description' => 
    array (
      0 => 'Implement a constexpr matrix type for small fixed-size matrices and a multiply function that can be evaluated at compile time. The result must be usable in a static_assert and must also run efficiently at runtime.',
      1 => 'The implementation must avoid uninitialised reads and must allow constant evaluation under C++20.',
    ),
    'examples' => 
    array (
      0 => 
      array (
        'input' => 'constexpr Matrix<int, 2, 2> a{{{1, 2}, {3, 4}}}; constexpr Matrix<int, 2, 2> b{{{2, 0}, {1, 2}}}; constexpr auto c = multiply(a, b);',
        'output' => 'c(0, 0) == 4, c(1, 1) == 8',
        'explanation' => 'Multiplication evaluated at compile time.',
      ),
    ),
    'constraints' => 
    array (
      0 => 'Dimensions are compile-time constants.',
      1 => 'multiply must be usable in a constant expression.',
      2 => 'No dynamic allocation.',
      3 => 'Runtime path must vectorise with -O3.',
    ),
    'follow_up' => 'Can you add compile-time transposition and identity matrices?',
    'editorial' => 
    array (
      'insight' => 'Constexpr algorithms in C++20 can use loops provided all memory is cleanly initialised.',
      'steps' => 
      array (
        0 => 'Zero-initialise data storage.',
        1 => 'Implement 3-nested loop multiply.',
        2 => 'Mark accessors and multiplication constexpr.',
      ),
      'time_complexity' => 'O(R * K * C)',
      'space_complexity' => 'O(R * C)',
    ),
    'starter_code' => 
    array (
      'cpp' => '#include <cstddef>

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
    // TODO: perform constexpr matrix multiplication.
    return result;
}

}  // namespace arena',
    ),
    'testing' => 
    array (
      'judge' => 'Tests static_assert evaluation in compiler, then benchmarks 32x32 runtime throughput at -O3.',
      'time_limit' => '2 seconds',
      'memory_limit' => '256 MB',
      'languages' => 
      array (
        0 => 'C++ 20',
      ),
      'notes' => 
      array (
        0 => 'All methods must be constexpr.',
      ),
      'sample_tests' => 
      array (
        0 => 
        array (
          'name' => 'Compile-time 2x2',
          'input' => '2x2 multiply',
          'expected' => 'verified via static_assert',
          'hidden' => false,
        ),
      ),
    ),
    'hints' => 
    array (
      0 => 'Zero-initialise data storage.',
      1 => 'Ensure all indexing is bounds-safe for constant evaluation.',
    ),
    'discipline' => 'Performance',
    'topic' => 'Compile-Time Numerics',
    'companies' => 
    array (
      0 => 'NVIDIA',
      1 => 'Apple',
    ),
  ),
);
