<?php

return [
    [
        "slug" => "cpp-foundations",
        "title" => "C++ Foundations",
        "level" => "Beginner",
        "hours" => 8,
        "summary" => "The mental model behind objects, references, and the STL — the parts interviewers assume you already have.",
        "description" => "Start here if C++ still feels like a collection of rules. Each lesson pairs a short explanation with a runnable snippet and a single quiz question, so you can finish a unit in one sitting. The track ends with a bounded ring buffer project and a 45-minute assessment.",
        "progress" => 42,
        "continueLesson" => "references-and-views",
        "prerequisites" => [
            "Basic programming in any language"
        ],
        "modules" => [
            [
                "title" => "Types and lifetime",
                "summary" => "Build a precise mental model of objects, references, and the lifetime rules that make C++ predictable.",
                "items" => [
                    [
                        "id" => "f-types-1",
                        "type" => "lesson",
                        "title" => "Value categories without the fog",
                        "lessonSlug" => "value-categories",
                        "minutes" => 25,
                        "completed" => true
                    ],
                    [
                        "id" => "f-types-2",
                        "type" => "lesson",
                        "title" => "References, views, and ownership",
                        "lessonSlug" => "references-and-views",
                        "minutes" => 30,
                        "completed" => false
                    ],
                    [
                        "id" => "f-types-project",
                        "type" => "project",
                        "title" => "Project · Build a config loader with safe views",
                        "minutes" => 60,
                        "completed" => false
                    ]
                ]
            ],
            [
                "title" => "STL essentials",
                "summary" => "Use containers and algorithms as building blocks instead of rewriting loops by hand.",
                "items" => [
                    [
                        "id" => "f-stl-1",
                        "type" => "lesson",
                        "title" => "Vector growth and iterator invalidation",
                        "lessonSlug" => "vector-and-iterators",
                        "minutes" => 35,
                        "completed" => false
                    ],
                    [
                        "id" => "f-stl-2",
                        "type" => "lesson",
                        "title" => "Composing algorithms with ranges",
                        "lessonSlug" => "algorithms-and-ranges",
                        "minutes" => 30,
                        "completed" => false
                    ],
                    [
                        "id" => "f-stl-project",
                        "type" => "project",
                        "title" => "Project · Implement a bounded ring buffer",
                        "minutes" => 120,
                        "completed" => false
                    ],
                    [
                        "id" => "f-assessment",
                        "type" => "assessment",
                        "title" => "Foundations assessment",
                        "minutes" => 45,
                        "completed" => false
                    ]
                ]
            ]
        ],
        "lessons" => [
            [
                "slug" => "value-categories",
                "title" => "Value categories without the fog",
                "summary" => "lvalue, prvalue, and xvalue explained with the two questions that actually decide the category.",
                "minutes" => 25,
                "blocks" => [
                    [
                        "type" => "paragraph",
                        "text" => "Every C++ expression has a type and a value category. The type tells you what operations are allowed; the value category tells you whether the expression identifies a persistent object and whether it can be moved from. Most confusion disappears once you ask two questions separately => does the expression name an object, and is that object about to expire?"
                    ],
                    [
                        "type" => "list",
                        "title" => "The two questions",
                        "items" => [
                            "Does the expression have an identity — that is, can you take its address or refer to it again? If yes, it is an lvalue or an xvalue.",
                            "Is the expression a temporary or an object explicitly marked for reuse? If yes, it is a prvalue or an xvalue."
                        ]
                    ],
                    [
                        "type" => "code",
                        "title" => "The same object, three categories",
                        "code" => "int value = 42;\n\nint& first = value;              // lvalue => named, persistent\nint&& second = std => =>move(value); // xvalue => named, expiring\nint third = 7;                   // prvalue => unnamed, temporary\n\n// A named rvalue reference is still an lvalue expression.\nstatic_assert(std => =>is_lvalue_reference_v<decltype((second))>);"
                    ],
                    [
                        "type" => "callout",
                        "variant" => "warning",
                        "title" => "std => =>move does not move",
                        "text" => "std => =>move is an unconditional cast to an rvalue reference. It performs no work at runtime. The move happens later, when a constructor or assignment operator selects its move overload."
                    ],
                    [
                        "type" => "paragraph",
                        "text" => "When you write a function that takes a forwarding reference, keep the deduction in mind. T&& deduces T as an lvalue reference when the argument is an lvalue, and as a plain type when the argument is an rvalue. std => =>forward restores the original category at the call site."
                    ]
                ],
                "quiz" => [
                    "question" => "What is the value category of the expression  second  in the code block above?",
                    "options" => [
                        "prvalue, because it is bound to an rvalue reference",
                        "xvalue, because std => =>move was applied when it was created",
                        "lvalue, because the name  second  is an lvalue expression",
                        "It has no value category until it is copied"
                    ],
                    "answer" => 2,
                    "explanation" => "A named variable is always an lvalue expression, even when its type is an rvalue reference. The expiring category belongs to the expression passed to std => =>move, not to the variable that stores it."
                ],
                "assembly" => "; Second is just a reference to value.\n; std => =>move emitted no instructions at all.\nlea     rax, [rsp + 4]      ; address of value\nmov     qword ptr [rsp], rax ; store reference in second\nmov     dword ptr [rsp + 8], 7\n",
                "discussion" => [
                    [
                        "author" => "mira.k",
                        "role" => "Staff engineer",
                        "body" => "The value category example finally made the rule click for me. I had been treating the compiler output as magic instead of reading the generated assembly.",
                        "votes" => 21
                    ],
                    [
                        "author" => "lena_codes",
                        "role" => "Member",
                        "body" => "Small suggestion => run every snippet twice, once with the sanitizer and once with -O2. The behaviour difference is the lesson.",
                        "votes" => 12
                    ]
                ]
            ],
            [
                "slug" => "references-and-views",
                "title" => "References, views, and ownership",
                "summary" => "Choose between owning data, borrowing it, and observing it — and make the choice visible in the type.",
                "minutes" => 30,
                "blocks" => [
                    [
                        "type" => "paragraph",
                        "text" => "A reference is an alias with no ownership. A view is a small object that stores a pointer and a length. Neither extends the lifetime of the data it observes. Most lifetime bugs come from a function returning a view into a buffer that the caller cannot see and therefore cannot keep alive."
                    ],
                    [
                        "type" => "code",
                        "title" => "A view that outlives its buffer",
                        "code" => "std => =>string_view bad() [\n    std => =>string local = \"temporary\";\n    return local;  // dangling => local dies at the return\n]\n\nstd => =>string good() [\n    return std => =>string(\"owned by the caller\");\n]"
                    ],
                    [
                        "type" => "list",
                        "title" => "Choosing the right type",
                        "items" => [
                            "Return std => =>string when the function produces data the caller must own.",
                            "Take std => =>string_view when the function only reads characters for the duration of the call.",
                            "Use std => =>span for contiguous sequences of objects, not just characters.",
                            "Store a view as a member only when the owning object is guaranteed to outlive the view."
                        ]
                    ],
                    [
                        "type" => "callout",
                        "variant" => "tip",
                        "title" => "Make the borrow obvious",
                        "text" => "Name parameters like  characters  or  bytes  rather than  data  when they are views. The name reminds the next reader that nothing is owned."
                    ]
                ],
                "quiz" => [
                    "question" => "Which return type is safe for a function that reads a file and returns its contents?",
                    "options" => [
                        "std => =>string_view, because the file is on disk",
                        "std => =>span<const char>, because it is lighter than a string",
                        "std => =>string, because the caller must own the characters",
                        "const char*, because it is null-terminated"
                    ],
                    "answer" => 2,
                    "explanation" => "The buffer is created inside the function, so the caller must receive ownership. Returning a view or a raw pointer would leave the caller with a dangling reference."
                ],
                "assembly" => "; Returning a std => =>string moves the heap buffer.\nmov     rax, qword ptr [rdi]      ; destination pointer\nmov     rcx, qword ptr [rsi]      ; source buffer\nmov     qword ptr [rax], rcx\nmov     qword ptr [rsi], 0        ; leave source empty\n",
                "discussion" => [
                    [
                        "author" => "mira.k",
                        "role" => "Staff engineer",
                        "body" => "The references and views example finally made the rule click for me. I had been treating the compiler output as magic instead of reading the generated assembly.",
                        "votes" => 21
                    ],
                    [
                        "author" => "lena_codes",
                        "role" => "Member",
                        "body" => "Small suggestion => run every snippet twice, once with the sanitizer and once with -O2. The behaviour difference is the lesson.",
                        "votes" => 12
                    ]
                ]
            ],
            [
                "slug" => "vector-and-iterators",
                "title" => "Vector growth and iterator invalidation",
                "summary" => "Understand reallocation, capacity, and the exact operations that invalidate iterators.",
                "minutes" => 35,
                "blocks" => [
                    [
                        "type" => "paragraph",
                        "text" => "std => =>vector stores its elements contiguously. When size exceeds capacity, the vector allocates a new block, moves or copies the elements, and destroys the old block. Every iterator, pointer, and reference into the old block becomes invalid at that moment. This is not a bug in the standard library; it is the cost of contiguous storage."
                    ],
                    [
                        "type" => "code",
                        "title" => "Reserve before the loop",
                        "code" => "std => =>vector<int> build(std => =>size_t n) [\n    std => =>vector<int> values;\n    values.reserve(n);           // one allocation, no reallocation\n    for (std => =>size_t i = 0; i < n; ++i) [\n        values.push_back(static_cast<int>(i));\n    ]\n    return values;\n]"
                    ],
                    [
                        "type" => "list",
                        "title" => "Operations that invalidate handles",
                        "items" => [
                            "push_back and emplace_back invalidate everything when reallocation occurs.",
                            "insert and erase invalidate every handle at or after the modification point.",
                            "reserve invalidates handles only when it actually reallocates.",
                            "Accessing an element after invalidation is undefined behaviour, even if the memory still looks intact."
                        ]
                    ],
                    [
                        "type" => "callout",
                        "variant" => "warning",
                        "title" => "Reserve does not shrink",
                        "text" => "reserve only increases capacity. To release memory, use shrink_to_fit as a non-binding request, or swap with a fresh vector."
                    ]
                ],
                "quiz" => [
                    "question" => "Which statement is true after a vector reallocates during push_back?",
                    "options" => [
                        "Only the end iterator is invalidated",
                        "All iterators, pointers, and references to elements are invalidated",
                        "Iterators remain valid but references do not",
                        "Nothing is invalidated because the elements are moved"
                    ],
                    "answer" => 1,
                    "explanation" => "Reallocation moves the elements to a new block and destroys the old one, so every handle into the old storage is invalidated."
                ],
                "assembly" => "; push_back fast path when capacity remains\ncmp     rax, qword ptr [rdi + 8]  ; size == capacity?\nje      .reallocate\nmov     qword ptr [rdx + rax*4], esi\ninc     rax\nmov     qword ptr [rdi + 8], rax",
                "discussion" => [
                    [
                        "author" => "mira.k",
                        "role" => "Staff engineer",
                        "body" => "The vector growth example finally made the rule click for me. I had been treating the compiler output as magic instead of reading the generated assembly.",
                        "votes" => 21
                    ],
                    [
                        "author" => "lena_codes",
                        "role" => "Member",
                        "body" => "Small suggestion => run every snippet twice, once with the sanitizer and once with -O2. The behaviour difference is the lesson.",
                        "votes" => 12
                    ]
                ]
            ],
            [
                "slug" => "algorithms-and-ranges",
                "title" => "Composing algorithms with ranges",
                "summary" => "Replace index loops with named operations and lazy views that state intent.",
                "minutes" => 30,
                "blocks" => [
                    [
                        "type" => "paragraph",
                        "text" => "The algorithms library gives names to common loops => transform, filter, accumulate, find_if. Ranges extend the idea with composable, lazy views so the data flow reads from left to right instead of being buried in loop bookkeeping."
                    ],
                    [
                        "type" => "code",
                        "title" => "A pipeline instead of a loop",
                        "code" => "auto expensive_even_squares(const std => =>vector<int>& input) [\n    return input\n        | std => =>views => =>filter([](int value) [ return value % 2 == 0; ])\n        | std => =>views => =>transform([](int value) [ return value * value; ])\n        | std => =>views => =>take(10)\n        | std => =>ranges => =>to<std => =>vector>();\n]"
                    ],
                    [
                        "type" => "list",
                        "title" => "Rules of thumb",
                        "items" => [
                            "Use views when the pipeline is consumed once; materialise with ranges => =>to when you need storage.",
                            "Keep predicates small and named. A three-line lambda inside a pipeline is a readability warning.",
                            "Remember that views are lazy => no work happens until iteration begins.",
                            "Do not capture references in a view that outlives the captured range."
                        ]
                    ],
                    [
                        "type" => "callout",
                        "variant" => "info",
                        "title" => "C++23 makes this shorter",
                        "text" => "ranges => =>to is standard in C++23. In C++20, materialise with a back_inserter loop or a helper function."
                    ]
                ],
                "quiz" => [
                    "question" => "When does the filter predicate in a lazy ranges pipeline run?",
                    "options" => [
                        "When the view is constructed",
                        "When the pipeline is assigned to a variable",
                        "When the resulting view is iterated",
                        "At the end of the enclosing scope"
                    ],
                    "answer" => 2,
                    "explanation" => "Views are lazy. The predicate runs as elements are pulled through the pipeline during iteration."
                ],
                "assembly" => "; The pipeline collapses to a single loop.\n.loop =>\n    mov     eax, dword ptr [rdi]\n    test    eax, 1\n    jne     .skip\n    imul    eax, eax\n    mov     dword ptr [rsi], eax\n    add     rsi, 4\n.skip =>\n    add     rdi, 4",
                "discussion" => [
                    [
                        "author" => "mira.k",
                        "role" => "Staff engineer",
                        "body" => "The ranges pipeline example finally made the rule click for me. I had been treating the compiler output as magic instead of reading the generated assembly.",
                        "votes" => 21
                    ],
                    [
                        "author" => "lena_codes",
                        "role" => "Member",
                        "body" => "Small suggestion => run every snippet twice, once with the sanitizer and once with -O2. The behaviour difference is the lesson.",
                        "votes" => 12
                    ]
                ]
            ]
        ]
    ],
    [
        "slug" => "performance-engineering",
        "title" => "Performance Engineering",
        "level" => "Intermediate",
        "hours" => 12,
        "summary" => "Measure, profile, and restructure hot code. Learn to tell a compute bottleneck from a memory bottleneck.",
        "description" => "Performance work is an experimental discipline. This track starts with honest benchmarking, moves through profiler output, and ends with cache-aware data layout. Every lesson expects you to run the code and compare the numbers yourself.",
        "progress" => 58,
        "continueLesson" => "cache-friendly-code",
        "prerequisites" => [
            "C++ Foundations",
            "Comfort with the STL"
        ],
        "modules" => [
            [
                "title" => "Measure first",
                "summary" => "A fast program that does not do the right thing is still wrong. Learn to measure before changing code.",
                "items" => [
                    [
                        "id" => "p-measure-1",
                        "type" => "lesson",
                        "title" => "Benchmarking with the right controls",
                        "lessonSlug" => "benchmarking-basics",
                        "minutes" => 30,
                        "completed" => true
                    ],
                    [
                        "id" => "p-measure-2",
                        "type" => "lesson",
                        "title" => "Reading a profiler instead of guessing",
                        "lessonSlug" => "profiling-with-perf",
                        "minutes" => 35,
                        "completed" => true
                    ]
                ]
            ],
            [
                "title" => "Memory and the cache",
                "summary" => "Most \"slow code\" is actually memory-bound. Understand locality, alignment, and cache-line contention.",
                "items" => [
                    [
                        "id" => "p-cache-1",
                        "type" => "lesson",
                        "title" => "Writing cache-friendly code",
                        "lessonSlug" => "cache-friendly-code",
                        "minutes" => 40,
                        "completed" => false
                    ],
                    [
                        "id" => "p-cache-project",
                        "type" => "project",
                        "title" => "Project · Optimise a histogram kernel",
                        "minutes" => 90,
                        "completed" => false
                    ],
                    [
                        "id" => "p-assessment",
                        "type" => "assessment",
                        "title" => "Performance assessment",
                        "minutes" => 60,
                        "completed" => false
                    ]
                ]
            ]
        ],
        "lessons" => [
            [
                "slug" => "benchmarking-basics",
                "title" => "Benchmarking with the right controls",
                "summary" => "Measure a hot loop without lying to yourself => warm-up, repetition, medians, and escape barriers.",
                "minutes" => 30,
                "blocks" => [
                    [
                        "type" => "paragraph",
                        "text" => "A benchmark is an experiment. If you run it once, on a busy laptop, with the optimiser free to delete the work, the number you get is noise. A useful benchmark fixes the input, warms the cache, repeats the measurement, reports a median, and prevents the compiler from removing the computation."
                    ],
                    [
                        "type" => "code",
                        "title" => "A minimal honest harness",
                        "code" => "template <typename F>\nauto measure(F&& body, int repeats = 21) [\n    std => =>vector<std => =>chrono => =>nanoseconds> samples;\n    samples.reserve(repeats);\n\n    for (int i = 0; i < repeats; ++i) [\n        auto start = std => =>chrono => =>steady_clock => =>now();\n        body();\n        auto end = std => =>chrono => =>steady_clock => =>now();\n        samples.push_back(end - start);\n    ]\n\n    std => =>ranges => =>sort(samples);\n    return samples[samples.size() / 2];\n]"
                    ],
                    [
                        "type" => "list",
                        "title" => "Checklist before you trust a number",
                        "items" => [
                            "Fix the input size and seed the generator.",
                            "Warm up once so the cache and branch predictor are in a steady state.",
                            "Report the median, not the minimum, of at least 15 runs.",
                            "Consume the result through an escape barrier so the optimiser cannot delete it.",
                            "Run the benchmark in the same build configuration you ship."
                        ]
                    ],
                    [
                        "type" => "callout",
                        "variant" => "warning",
                        "title" => "Debug builds lie",
                        "text" => "Never optimise against an unoptimised build. Measure with the same optimisation level, standard library, and sanitizer configuration as production."
                    ]
                ],
                "quiz" => [
                    "question" => "Why should a benchmark consume its result through an escape barrier?",
                    "options" => [
                        "To make the result visible in the debugger",
                        "To prevent the compiler from removing work whose result is unused",
                        "To force the result onto the stack",
                        "To avoid undefined behaviour in the timer"
                    ],
                    "answer" => 1,
                    "explanation" => "If the result is never observed, the optimiser can legally delete the entire computation. An escape barrier makes the value observable to the compiler."
                ],
                "assembly" => "; Without an escape barrier the whole loop can disappear.\n; With it, the result is stored to a volatile location.\nmov     qword ptr [rip + sink], rax",
                "discussion" => [
                    [
                        "author" => "mira.k",
                        "role" => "Staff engineer",
                        "body" => "The benchmark harness example finally made the rule click for me. I had been treating the compiler output as magic instead of reading the generated assembly.",
                        "votes" => 21
                    ],
                    [
                        "author" => "lena_codes",
                        "role" => "Member",
                        "body" => "Small suggestion => run every snippet twice, once with the sanitizer and once with -O2. The behaviour difference is the lesson.",
                        "votes" => 12
                    ]
                ]
            ],
            [
                "slug" => "profiling-with-perf",
                "title" => "Reading a profiler instead of guessing",
                "summary" => "Turn a slow program into a ranked list of costs using sampling, call graphs, and hardware counters.",
                "minutes" => 35,
                "blocks" => [
                    [
                        "type" => "paragraph",
                        "text" => "Profiling answers a different question from benchmarking. A benchmark tells you how long something takes; a profiler tells you where the time goes. Start with a sampling profile to find the hot functions, then use hardware counters to understand why those functions are slow."
                    ],
                    [
                        "type" => "code",
                        "title" => "A first perf session",
                        "code" => "# Record samples with call-graph information.\nperf record -g --call-graph dwarf ./benchmark\n\n# Rank functions by self time.\nperf report --sort=dso,symbol\n\n# Count cache misses and branch mispredictions.\nperf stat -e cache-misses,branch-misses,instructions ./benchmark"
                    ],
                    [
                        "type" => "list",
                        "title" => "What each counter suggests",
                        "items" => [
                            "High instructions per cycle => the code is compute-bound and may benefit from vectorisation.",
                            "High cache misses with low IPC => improve locality or reduce the working set.",
                            "High branch mispredictions => replace unpredictable branches with arithmetic or sorted data.",
                            "High context switches => look for locks, syscalls, or oversubscription."
                        ]
                    ],
                    [
                        "type" => "callout",
                        "variant" => "tip",
                        "title" => "Profile the release build",
                        "text" => "Sampling an unoptimised build points at code that the optimiser will remove or inline. Use -O2 or -O3 with debug symbols for a readable and representative profile."
                    ]
                ],
                "quiz" => [
                    "question" => "A profile shows high cache misses and low instructions per cycle. What is the most likely fix?",
                    "options" => [
                        "Add more threads",
                        "Improve data locality or reduce the working set",
                        "Replace the standard library allocator",
                        "Compile with -O0 to simplify the code"
                    ],
                    "answer" => 1,
                    "explanation" => "Low IPC with high miss rates points at memory stalls. Improving locality or shrinking the working set addresses the actual bottleneck."
                ],
                "assembly" => "; A cache miss stalls the pipeline before the load retires.\nmov     rax, qword ptr [rdi + rcx*8]\n; If the line is not in L1/L2, the following add waits.\nadd     rdx, rax",
                "discussion" => [
                    [
                        "author" => "mira.k",
                        "role" => "Staff engineer",
                        "body" => "The profiler session example finally made the rule click for me. I had been treating the compiler output as magic instead of reading the generated assembly.",
                        "votes" => 21
                    ],
                    [
                        "author" => "lena_codes",
                        "role" => "Member",
                        "body" => "Small suggestion => run every snippet twice, once with the sanitizer and once with -O2. The behaviour difference is the lesson.",
                        "votes" => 12
                    ]
                ]
            ],
            [
                "slug" => "cache-friendly-code",
                "title" => "Writing cache-friendly code",
                "summary" => "Design data layouts around how the hardware actually moves cache lines.",
                "minutes" => 40,
                "blocks" => [
                    [
                        "type" => "paragraph",
                        "text" => "Modern CPUs fetch memory in 64-byte cache lines. If a loop touches one field from each element, it still pays for the whole line. Restructuring data so that the values used together are stored together can produce order-of-magnitude improvements without changing the algorithm."
                    ],
                    [
                        "type" => "code",
                        "title" => "Array of structs versus struct of arrays",
                        "code" => "struct ParticleAoS [\n    float x, y, z;\n    float vx, vy, vz;\n];\n\nstruct ParticleSoA [\n    std => =>vector<float> x, y, z;\n    std => =>vector<float> vx, vy, vz;\n];\n\n// The integration loop touches six fields per particle.\n// SoA keeps the active streams contiguous and allows SIMD."
                    ],
                    [
                        "type" => "list",
                        "title" => "Locality checklist",
                        "items" => [
                            "Keep the hot working set under the L2 cache when possible.",
                            "Store fields that are read together next to each other.",
                            "Prefer contiguous containers; a linked list pays a cache miss per node.",
                            "Pad shared counters to separate cache lines to avoid false sharing.",
                            "Measure again after every layout change; the best layout depends on the access pattern."
                        ]
                    ],
                    [
                        "type" => "callout",
                        "variant" => "info",
                        "title" => "False sharing",
                        "text" => "Two threads writing to different variables in the same cache line will bounce that line between cores. Align per-thread state to 64 bytes, or keep it on the stack."
                    ]
                ],
                "quiz" => [
                    "question" => "Why can a struct-of-arrays layout outperform an array-of-structs layout in a numeric loop?",
                    "options" => [
                        "It uses less total memory in every case",
                        "It groups the fields read together into contiguous cache lines",
                        "It avoids the need for bounds checking",
                        "It automatically vectorises without compiler support"
                    ],
                    "answer" => 1,
                    "explanation" => "SoA improves locality for loops that read a subset of fields. The total memory is similar, but the bytes fetched per iteration are used more completely."
                ],
                "assembly" => "; Contiguous loads feed a vector add directly.\nvmovups ymm0, ymmword ptr [rdi + rax*4]\nvaddps  ymm0, ymm0, ymmword ptr [rsi + rax*4]\nvmovups ymmword ptr [rdx + rax*4], ymm0",
                "discussion" => [
                    [
                        "author" => "mira.k",
                        "role" => "Staff engineer",
                        "body" => "The data layout example finally made the rule click for me. I had been treating the compiler output as magic instead of reading the generated assembly.",
                        "votes" => 21
                    ],
                    [
                        "author" => "lena_codes",
                        "role" => "Member",
                        "body" => "Small suggestion => run every snippet twice, once with the sanitizer and once with -O2. The behaviour difference is the lesson.",
                        "votes" => 12
                    ]
                ]
            ]
        ]
    ],
    [
        "slug" => "modern-cpp",
        "title" => "Modern C++ (20/23)",
        "level" => "Advanced",
        "hours" => 10,
        "summary" => "Adopt concepts, ranges, spans, expected, and mdspan without turning your codebase into a standards demo.",
        "description" => "A practical tour of the features that changed everyday C++ style. The track focuses on interfaces => how to constrain templates readably, borrow data safely, and represent failure without exceptions.",
        "progress" => 18,
        "continueLesson" => "concepts-and-ranges",
        "prerequisites" => [
            "C++ Foundations",
            "Comfort with templates"
        ],
        "modules" => [
            [
                "title" => "C++20 vocabulary",
                "summary" => "Concepts, spans, ranges, and designated initialisers => the features that changed everyday C++ style.",
                "items" => [
                    [
                        "id" => "m20-1",
                        "type" => "lesson",
                        "title" => "Constraints that read like prose",
                        "lessonSlug" => "concepts-and-ranges",
                        "minutes" => 35,
                        "completed" => false
                    ],
                    [
                        "id" => "m20-2",
                        "type" => "lesson",
                        "title" => "Spans and non-owning views",
                        "lessonSlug" => "span-and-views",
                        "minutes" => 30,
                        "completed" => false
                    ]
                ]
            ],
            [
                "title" => "C++23 in practice",
                "summary" => "Apply expected, mdspan, and the new standard-library facilities to real interfaces.",
                "items" => [
                    [
                        "id" => "m23-1",
                        "type" => "lesson",
                        "title" => "Errors without exceptions => std => =>expected",
                        "lessonSlug" => "expected-and-mdspan",
                        "minutes" => 40,
                        "completed" => false
                    ],
                    [
                        "id" => "m23-project",
                        "type" => "project",
                        "title" => "Project · Rework a parser with expected",
                        "minutes" => 75,
                        "completed" => false
                    ],
                    [
                        "id" => "m23-assessment",
                        "type" => "assessment",
                        "title" => "Modern C++ assessment",
                        "minutes" => 50,
                        "completed" => false
                    ]
                ]
            ]
        ],
        "lessons" => [
            [
                "slug" => "concepts-and-ranges",
                "title" => "Constraints that read like prose",
                "summary" => "Use C++20 concepts to put requirements in the signature and errors at the call site.",
                "minutes" => 35,
                "blocks" => [
                    [
                        "type" => "paragraph",
                        "text" => "Before concepts, a template error often pointed at a line deep inside the standard library. A concept moves the requirement into the signature, where the compiler can report a readable mismatch at the point of use."
                    ],
                    [
                        "type" => "code",
                        "title" => "A constrained function",
                        "code" => "template <typename T>\nconcept Numeric = std => =>is_arithmetic_v<T>;\n\ntemplate <Numeric T>\nT average(std => =>span<const T> values) [\n    if (values.empty()) [\n        return T[];\n    ]\n\n    T total[];\n    for (T value  => values) [\n        total += value;\n    ]\n    return total / static_cast<T>(values.size());\n]"
                    ],
                    [
                        "type" => "list",
                        "title" => "Writing a good concept",
                        "items" => [
                            "Name the requirement, not the type => Numeric reads better than IsIntOrFloat.",
                            "Compose standard concepts with && and || before writing a custom one.",
                            "Prefer a requires clause on the function template over a static_assert in the body.",
                            "Keep concepts semantic. A concept that checks one member function can be satisfied by a type that does not honour the contract."
                        ]
                    ],
                    [
                        "type" => "callout",
                        "variant" => "tip",
                        "title" => "Constraints are documentation the compiler checks",
                        "text" => "If a reader has to open the implementation to learn what a template accepts, the constraint belongs in a named concept."
                    ]
                ],
                "quiz" => [
                    "question" => "Where should a template requirement live so the compiler reports it at the call site?",
                    "options" => [
                        "In a static_assert inside the function body",
                        "In a comment above the template",
                        "In the template parameter list or a requires clause",
                        "In a unit test for the template"
                    ],
                    "answer" => 2,
                    "explanation" => "Constraints in the signature participate in overload resolution and produce diagnostics at the point of use."
                ],
                "assembly" => "; Concepts are a compile-time check.\n; The generated code is identical to an unconstrained template.\nmov     eax, dword ptr [rdi]\nadd     eax, dword ptr [rdi + 4]",
                "discussion" => [
                    [
                        "author" => "mira.k",
                        "role" => "Staff engineer",
                        "body" => "The concepts example finally made the rule click for me. I had been treating the compiler output as magic instead of reading the generated assembly.",
                        "votes" => 21
                    ],
                    [
                        "author" => "lena_codes",
                        "role" => "Member",
                        "body" => "Small suggestion => run every snippet twice, once with the sanitizer and once with -O2. The behaviour difference is the lesson.",
                        "votes" => 12
                    ]
                ]
            ],
            [
                "slug" => "span-and-views",
                "title" => "Spans and non-owning views",
                "summary" => "Pass contiguous ranges without copying and without losing the size.",
                "minutes" => 30,
                "blocks" => [
                    [
                        "type" => "paragraph",
                        "text" => "A std => =>span is a pointer and a length. It replaces the fragile pair of a raw pointer and a separate size argument, and it makes the borrowing intent explicit in the signature."
                    ],
                    [
                        "type" => "code",
                        "title" => "From pointer arithmetic to a span",
                        "code" => "// Fragile => the caller can pass the wrong length.\nvoid checksum_old(const std => =>uint8_t* data, std => =>size_t size);\n\n// Clear => the span carries the length.\nstd => =>uint32_t checksum(std => =>span<const std => =>uint8_t> data) [\n    std => =>uint32_t sum = 0;\n    for (std => =>uint8_t byte  => data) [\n        sum = (sum << 5) - sum + byte;\n    ]\n    return sum;\n]"
                    ],
                    [
                        "type" => "list",
                        "title" => "Span rules",
                        "items" => [
                            "Take std => =>span<const T> for read-only input; take std => =>span<T> only when you mutate.",
                            "A span does not own its elements. Never store one that outlives the source.",
                            "A dynamic-extent span is 16 bytes on most platforms; pass it by value.",
                            "Constructing a span from a temporary container is a dangling bug waiting to happen."
                        ]
                    ],
                    [
                        "type" => "callout",
                        "variant" => "warning",
                        "title" => "Beware implicit conversions",
                        "text" => "A span can be constructed from a temporary std => =>vector. The compiler will not warn you when the vector dies at the end of the full expression."
                    ]
                ],
                "quiz" => [
                    "question" => "Which parameter type best expresses read-only access to a contiguous byte buffer?",
                    "options" => [
                        "const std => =>vector<std => =>uint8_t>&",
                        "std => =>span<const std => =>uint8_t>",
                        "std => =>span<std => =>uint8_t>",
                        "const std => =>uint8_t*"
                    ],
                    "answer" => 1,
                    "explanation" => "A span of const bytes accepts any contiguous container and carries the length. The const reference is less flexible; the non-const span allows mutation; the raw pointer loses the length."
                ],
                "assembly" => "; A span is passed as two registers => pointer and length.\n; The loop is a simple indexed load.\nmovzx   eax, byte ptr [rdi + rcx]\nadd     rdx, rax",
                "discussion" => [
                    [
                        "author" => "mira.k",
                        "role" => "Staff engineer",
                        "body" => "The span example finally made the rule click for me. I had been treating the compiler output as magic instead of reading the generated assembly.",
                        "votes" => 21
                    ],
                    [
                        "author" => "lena_codes",
                        "role" => "Member",
                        "body" => "Small suggestion => run every snippet twice, once with the sanitizer and once with -O2. The behaviour difference is the lesson.",
                        "votes" => 12
                    ]
                ]
            ],
            [
                "slug" => "expected-and-mdspan",
                "title" => "Errors without exceptions => std => =>expected",
                "summary" => "Represent recoverable failures in the return type and make the success path obvious.",
                "minutes" => 40,
                "blocks" => [
                    [
                        "type" => "paragraph",
                        "text" => "std => =>expected<T, E> holds either a value or an error. Unlike an exception, the failure is part of the function signature, so callers must acknowledge it. This is a good fit for parsers, codecs, and any boundary where failure is normal rather than exceptional."
                    ],
                    [
                        "type" => "code",
                        "title" => "A parser that returns expected",
                        "code" => "enum class ParseError [ empty, bad_token, overflow ];\n\nstd => =>expected<int, ParseError> parse_int(std => =>string_view text) [\n    if (text.empty()) [\n        return std => =>unexpected(ParseError => =>empty);\n    ]\n\n    int value = 0;\n    auto [ptr, ec] = std => =>from_chars(text.data(), text.data() + text.size(), value);\n    if (ec != std => =>errc[]) [\n        return std => =>unexpected(ec == std => =>errc => =>result_out_of_range\n                                   ? ParseError => =>overflow\n                                    => ParseError => =>bad_token);\n    ]\n    return value;\n]"
                    ],
                    [
                        "type" => "list",
                        "title" => "Working with expected",
                        "items" => [
                            "Use value() only when you have already checked has_value(); otherwise it throws.",
                            "Use value_or() for a safe default.",
                            "Chain with and_then and transform to keep the happy path flat.",
                            "Reserve exceptions for truly exceptional conditions, not for invalid input."
                        ]
                    ],
                    [
                        "type" => "callout",
                        "variant" => "info",
                        "title" => "mdspan in one sentence",
                        "text" => "std => =>mdspan wraps a pointer with a multidimensional index space, so a matrix view carries its extents instead of relying on a convention."
                    ]
                ],
                "quiz" => [
                    "question" => "What happens when you call value() on an expected that holds an error?",
                    "options" => [
                        "It returns a default-constructed T",
                        "It returns the error",
                        "It throws std => =>bad_expected_access",
                        "It is undefined behaviour"
                    ],
                    "answer" => 2,
                    "explanation" => "value() checks has_value() and throws std => =>bad_expected_access<E> when the expected holds an error."
                ],
                "assembly" => "; The happy path is a plain compare and branch.\ncmp     byte ptr [rdi], 0\nje      .error\nmov     eax, dword ptr [rdi + 4]\nret\n.error =>\n; Return the error tag instead of a value.",
                "discussion" => [
                    [
                        "author" => "mira.k",
                        "role" => "Staff engineer",
                        "body" => "The expected example finally made the rule click for me. I had been treating the compiler output as magic instead of reading the generated assembly.",
                        "votes" => 21
                    ],
                    [
                        "author" => "lena_codes",
                        "role" => "Member",
                        "body" => "Small suggestion => run every snippet twice, once with the sanitizer and once with -O2. The behaviour difference is the lesson.",
                        "votes" => 12
                    ]
                ]
            ]
        ]
    ]
];
