<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinalAverageCommentsSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $comments = [

            /* ======================
             * 70 – 100 (Excellent)
             * ====================== */
            ['min_score' => 70, 'max_score' => 100, 'comment' => 'An excellent performance, keep it up.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 70, 'max_score' => 100, 'comment' => 'Outstanding achievement; you have done very well.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 70, 'max_score' => 100, 'comment' => 'Excellent result! Maintain this level of performance.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 70, 'max_score' => 100, 'comment' => 'A brilliant performance; keep aiming higher.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 70, 'max_score' => 100, 'comment' => 'Very impressive work; your efforts are commendable.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 70, 'max_score' => 100, 'comment' => 'Exceptional performance; continue the good work.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 70, 'max_score' => 100, 'comment' => 'Excellent effort; you are setting a great example.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 70, 'max_score' => 100, 'comment' => 'A remarkable result; keep striving for excellence.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 70, 'max_score' => 100, 'comment' => 'Superb performance; consistency is key.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 70, 'max_score' => 100, 'comment' => 'Well done! Your hard work has paid off.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],

            /* ======================
             * 60 – 69.99 (Very Good)
             * ====================== */
            ['min_score' => 60, 'max_score' => 69.99, 'comment' => 'Good work! Keep striving for excellence.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 60, 'max_score' => 69.99, 'comment' => 'Well done! Aim a little higher next term.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 60, 'max_score' => 69.99, 'comment' => 'A solid performance, continue to build on it.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 60, 'max_score' => 69.99, 'comment' => 'Nice effort! Focus on your weaker areas.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 60, 'max_score' => 69.99, 'comment' => 'Good job! You’re on the right track.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 60, 'max_score' => 69.99, 'comment' => 'Well executed, but there’s room for improvement.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 60, 'max_score' => 69.99, 'comment' => 'Keep up the effort; you can achieve even more.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 60, 'max_score' => 69.99, 'comment' => 'Good performance! Stay consistent.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 60, 'max_score' => 69.99, 'comment' => 'You’ve done well, but challenge yourself further.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 60, 'max_score' => 69.99, 'comment' => 'A commendable effort; aim higher next time.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],

            /* ======================
             * 50 – 59.99 (Good)
             * ====================== */
            ['min_score' => 50, 'max_score' => 59.99, 'comment' => 'Fair performance, focus more on your studies.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 50, 'max_score' => 59.99, 'comment' => 'You did okay, but improvement is needed.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 50, 'max_score' => 59.99, 'comment' => 'Keep trying! Review your weaker areas.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 50, 'max_score' => 59.99, 'comment' => 'Average work; aim to do better next term.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 50, 'max_score' => 59.99, 'comment' => 'Your efforts are noticeable; push further.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 50, 'max_score' => 59.99, 'comment' => 'You’re progressing, but more consistency is required.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 50, 'max_score' => 59.99, 'comment' => 'Do not be discouraged; more effort will improve results.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 50, 'max_score' => 59.99, 'comment' => 'Moderate performance; focus on understanding key concepts.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 50, 'max_score' => 59.99, 'comment' => 'Fair effort; take time to revise your lessons.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 50, 'max_score' => 59.99, 'comment' => 'Keep practicing; improvement is achievable.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],

            /* ======================
             * 45 – 49.99 (Pass)
             * ====================== */
            ['min_score' => 45, 'max_score' => 49.99, 'comment' => 'Below average; extra practice is required.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 45, 'max_score' => 49.99, 'comment' => 'You need to work harder to improve your scores.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 45, 'max_score' => 49.99, 'comment' => 'Pay attention to your weak areas and revise regularly.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 45, 'max_score' => 49.99, 'comment' => 'Moderate effort; aim to understand the concepts better.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 45, 'max_score' => 49.99, 'comment' => 'Needs improvement; consider additional study time.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 45, 'max_score' => 49.99, 'comment' => 'Your results can improve with more focus.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 45, 'max_score' => 49.99, 'comment' => 'Do not give up; put in consistent effort.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 45, 'max_score' => 49.99, 'comment' => 'Below expectation; seek guidance where needed.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 45, 'max_score' => 49.99, 'comment' => 'Try to engage more actively in learning activities.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 45, 'max_score' => 49.99, 'comment' => 'Focus on your studies to achieve better results.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],

            /* ======================
             * 40 – 44.99 (Weak)
             * ====================== */
            ['min_score' => 40, 'max_score' => 44.99, 'comment' => 'Poor performance; significant improvement is needed.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 40, 'max_score' => 44.99, 'comment' => 'You must work harder to meet expectations.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 40, 'max_score' => 44.99, 'comment' => 'Focus on understanding your lessons better.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 40, 'max_score' => 44.99, 'comment' => 'Results are unsatisfactory; seek extra help if needed.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 40, 'max_score' => 44.99, 'comment' => 'Weak performance; consistent effort required.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 40, 'max_score' => 44.99, 'comment' => 'Do not be discouraged, but work harder next term.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 40, 'max_score' => 44.99, 'comment' => 'Pay close attention to areas you are struggling with.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 40, 'max_score' => 44.99, 'comment' => 'Improvement is necessary; dedicate more time to studying.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 40, 'max_score' => 44.99, 'comment' => 'Below standard; ask teachers for guidance.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 40, 'max_score' => 44.99, 'comment' => 'You can do better; focus and practice more.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],

            /* ======================
             * 0 – 39.99 (Fail)
             * ====================== */
            ['min_score' => 0, 'max_score' => 39.99, 'comment' => 'Very poor performance; urgent improvement required.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 0, 'max_score' => 39.99, 'comment' => 'Your scores are low; extra effort is essential.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 0, 'max_score' => 39.99, 'comment' => 'Seek help to understand the lessons better.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 0, 'max_score' => 39.99, 'comment' => 'Work harder to avoid falling behind.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 0, 'max_score' => 39.99, 'comment' => 'Significant improvement is necessary; revise daily.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 0, 'max_score' => 39.99, 'comment' => 'Do not be discouraged, but take action immediately.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 0, 'max_score' => 39.99, 'comment' => 'Very weak performance; focus on fundamentals.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 0, 'max_score' => 39.99, 'comment' => 'Your results are far below expectations; prioritize your studies.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 0, 'max_score' => 39.99, 'comment' => 'Consider extra tutorials or guidance sessions.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['min_score' => 0, 'max_score' => 39.99, 'comment' => 'Immediate action is required to improve your grades.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('final_average_comments')->truncate();
        DB::table('final_average_comments')->insert($comments);
    }
}
