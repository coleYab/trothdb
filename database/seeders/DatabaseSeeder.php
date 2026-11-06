<?php

namespace Database\Seeders;

use App\Models\Problem;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed problems and learning tracks
        $this->call([
            ProblemSeeder::class,
            ProblemTestSuiteSeeder::class,
            LearningSeeder::class,
        ]);

        // Remove any non-C++ submissions to strictly maintain C++ only platform
        Submission::where('language', '!=', 'cpp')->delete();

        // 2. Curated users with realistic competitive solutions
        $curatedUsers = [
            [
                'name' => 'Test User',
                'username' => 'testuser',
                'email' => 'test@example.com',
                'password' => Hash::make('password'),
                'problem_id' => 'two-sum',
                'language' => 'cpp',
                'score' => 100,
                'code' => '#include <vector>
#include <unordered_map>
using namespace std;

class Solution {
public:
    vector<int> twoSum(vector<int>& nums, int target) {
        unordered_map<int, int> lookup;
        for (int i = 0; i < nums.size(); ++i) {
            int complement = target - nums[i];
            if (lookup.count(complement)) {
                return {lookup[complement], i};
            }
            lookup[nums[i]] = i;
        }
        return {};
    }
};',
            ],
            [
                'name' => 'Sophia Taylor',
                'username' => 'sophia_cpp',
                'email' => 'sophia@example.com',
                'password' => Hash::make('password'),
                'problem_id' => 'two-sum',
                'language' => 'cpp',
                'score' => 100,
                'code' => '#include <vector>
#include <unordered_map>

class Solution {
public:
    std::vector<int> twoSum(std::vector<int>& nums, int target) {
        std::unordered_map<int, int> seen;
        for (int i = 0; i < static_cast<int>(nums.size()); ++i) {
            int complement = target - nums[i];
            auto it = seen.find(complement);
            if (it != seen.end()) {
                return {it->second, i};
            }
            seen[nums[i]] = i;
        }
        return {};
    }
};',
            ],
            [
                'name' => 'David Kim',
                'username' => 'dkim_lowlat',
                'email' => 'david@example.com',
                'password' => Hash::make('password'),
                'problem_id' => 'two-sum',
                'language' => 'cpp',
                'score' => 100,
                'code' => '#include <vector>
#include <unordered_map>

class Solution {
public:
    std::vector<int> twoSum(std::vector<int>& nums, int target) {
        std::unordered_map<int, int> pos;
        for (int i = 0; i < static_cast<int>(nums.size()); ++i) {
            int need = target - nums[i];
            if (pos.count(need)) {
                return {pos[need], i};
            }
            pos[nums[i]] = i;
        }
        return {};
    }
};',
            ],
            [
                'name' => 'Marcus Vance',
                'username' => 'mvance_sys',
                'email' => 'marcus@example.com',
                'password' => Hash::make('password'),
                'problem_id' => 'two-sum',
                'language' => 'cpp',
                'score' => 100,
                'code' => '#include <vector>

class Solution {
public:
    std::vector<int> twoSum(std::vector<int>& nums, int target) {
        int n = static_cast<int>(nums.size());
        for (int i = 0; i < n; ++i) {
            for (int j = i + 1; j < n; ++j) {
                if (nums[i] + nums[j] == target) {
                    return {i, j};
                }
            }
        }
        return {};
    }
};',
            ],
            [
                'name' => 'Alex Chen',
                'username' => 'alexc_quant',
                'email' => 'alex@example.com',
                'password' => Hash::make('password'),
                'problem_id' => 'ring-buffer',
                'language' => 'cpp',
                'score' => 100,
                'code' => '#include <atomic>
#include <cstddef>

template <typename T, size_t Cap>
class RingBuffer {
    static_assert((Cap & (Cap - 1)) == 0, "Capacity must be power of 2");
    alignas(64) std::atomic<size_t> head_{0};
    alignas(64) std::atomic<size_t> tail_{0};
    alignas(64) T buffer_[Cap];
public:
    bool push(const T& item) {
        size_t t = tail_.load(std::memory_order_relaxed);
        if (t - head_.load(std::memory_order_acquire) == Cap) return false;
        buffer_[t & (Cap - 1)] = item;
        tail_.store(t + 1, std::memory_order_release);
        return true;
    }
    bool pop(T& item) {
        size_t h = head_.load(std::memory_order_relaxed);
        if (h == tail_.load(std::memory_order_acquire)) return false;
        item = buffer_[h & (Cap - 1)];
        head_.store(h + 1, std::memory_order_release);
        return true;
    }
};',
            ],
            [
                'name' => 'Elena Rostova',
                'username' => 'elena_algo',
                'email' => 'elena@example.com',
                'password' => Hash::make('password'),
                'problem_id' => 'lru-cache',
                'language' => 'cpp',
                'score' => 100,
                'code' => '#include <unordered_map>
#include <list>

class LRUCache {
    int cap_;
    std::list<std::pair<int, int>> order_;
    std::unordered_map<int, std::list<std::pair<int, int>>::iterator> map_;
public:
    LRUCache(int capacity) : cap_(capacity) {}
    int get(int key) {
        auto it = map_.find(key);
        if (it == map_.end()) return -1;
        order_.splice(order_.begin(), order_, it->second);
        return it->second->second;
    }
    void put(int key, int value) {
        auto it = map_.find(key);
        if (it != map_.end()) {
            it->second->second = value;
            order_.splice(order_.begin(), order_, it->second);
            return;
        }
        if (order_.size() == cap_) {
            map_.erase(order_.back().first);
            order_.pop_back();
        }
        order_.emplace_front(key, value);
        map_[key] = order_.begin();
    }
};',
            ],
        ];

        foreach ($curatedUsers as $userData) {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'username' => $userData['username'],
                    'password' => $userData['password'],
                    'email_verified_at' => now(),
                ]
            );

            Submission::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'problem_id' => $userData['problem_id'],
                ],
                [
                    'language' => $userData['language'],
                    'code' => $userData['code'],
                    'score' => $userData['score'],
                    'status' => 'accepted',
                    'runtime_ms' => mt_rand(2, 8),
                    'memory_mb' => round(mt_rand(120, 160) / 10, 1),
                ]
            );
        }

        // 3. Ensure every user in database has at least one submission
        $allProblems = Problem::pluck('id')->all();
        foreach (User::all() as $user) {
            if (! Submission::where('user_id', $user->id)->exists()) {
                $probId = ! empty($allProblems) ? $allProblems[array_rand($allProblems)] : 'two-sum';
                Submission::create([
                    'user_id' => $user->id,
                    'problem_id' => $probId,
                    'language' => 'cpp',
                    'code' => "// Solution by {$user->name}\n#include <vector>\nusing namespace std;\nclass Solution { public: vector<int> twoSum(vector<int>& n, int t) { return {0, 1}; } };",
                    'score' => 100,
                    'status' => 'accepted',
                    'runtime_ms' => mt_rand(2, 6),
                    'memory_mb' => 14.2,
                ]);
            }
        }
    }
}
