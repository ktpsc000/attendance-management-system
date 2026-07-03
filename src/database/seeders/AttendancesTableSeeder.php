<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Models\Attendance;

class AttendancesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user1 = User::where('email', 'user1@example.com')->first();
        $user2 = User::where('email', 'user2@example.com')->first();
        $admin = User::where('email', 'user3@example.com')->first();

        $this->createUser1Attendance($user1);

        $this->createDummyAttendance($user2);

        $this->createDummyAttendance($admin);
    }

    private function createUser1Attendance(User $user)
    {
        $this->createPastFiveMonths($user);

        $this->createCurrentMonth($user);
    }

    private function createPastFiveMonths(User $user)
    {
        for ($i = 5; $i >= 1; $i--) {

            $month = now()->copy()->subMonths($i);
            $count = 0;

            for ($day = 1; $day <= $month->daysInMonth; $day++) {

                $date = Carbon::create(
                    $month->year,
                    $month->month,
                    $day
                );

                if ($date->isWeekend()) {
                    continue;
                }

                $this->createAttendance($user, $date, '09:00', '18:00');
                $count++;

                if ($count >= 15) {
                    break;
                }
            }
        }
    }

    private function createCurrentMonth(User $user)
    {
        $month = now();
        $dates = [];

        for ($day = 1; $day <= $month->daysInMonth; $day++) {
            $date = Carbon::create(
                $month->year,
                $month->month,
                $day
            );

            if ($date->isWeekend()) {
                continue;
            }

            $dates[] = $date;

            if (count($dates) >= 17) {
                break;
            }
        }

        $patterns = [
                ['09:00', '18:00', 10], // 通常勤務10日
                ['09:00', '20:00', 3],  // 残業3日
                ['09:30', '18:00', 2],  // 遅刻2日
                ['09:00', '17:00', 1],  // 早退1日
                ['08:00', '21:00', 1],  // 長時間労働1日
            ];

        $index = 0;

        foreach ($patterns as $pattern) {

            [$clockIn, $clockOut, $count] = $pattern;

            for ($i = 0; $i < $count; $i++) {

                $this->createAttendance(
                    $user,
                    $dates[$index],
                    $clockIn,
                    $clockOut
                );

                $index++;
            }
        }
    }

    private function createDummyAttendance(User $user)
    {
        $month = now();

        for ($day = 1; $day <= $month->daysInMonth; $day++) {

            $date = Carbon::create(
                $month->year,
                $month->month,
                $day
            );

            if ($date->isWeekend()) {
                continue;
            }

            $this->createAttendance(
                $user,
                $date,
                '09:00',
                '18:00'
            );
        }
    }

    private function createAttendance(
        User $user,
        Carbon $date,
        string $clockIn,
        string $clockOut
    ) {
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => $date,
            'clock_in_at' => Carbon::parse($clockIn),
            'clock_out_at' => Carbon::parse($clockOut),
        ]);

        $attendance->breaks()->create([
            'break_start_at' => Carbon::parse('12:00'),
            'break_end_at' => Carbon::parse('13:00'),
        ]);

        return $attendance;
    }
}
