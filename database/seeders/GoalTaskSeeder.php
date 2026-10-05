<?php

namespace Database\Seeders;

use App\Models\Goal;
use App\Models\Sdg;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GoalTaskSeeder extends Seeder
{
	public function run(): void
	{
		$projectManager = User::first();

		if (!$projectManager) {
			$this->command->error('No users found. Please seed users first.');
			return;
		}

		$programs = Sdg::all();

		if ($programs->isEmpty()) {
			$this->command->error('No programs found. Please seed the SdgSeeder first.');
			return;
		}

		// Template goals — {PROGRAM} gets replaced per program
		$goalsTemplate = [
			[
				'title' => 'Curriculum Standardization and Quality Control',
				'description' => 'To ensure the {PROGRAM} curriculum is industry-relevant, updated, and compliant with national education standards.',
				'type' => 'long term',
				'tasks' => [
					'Curriculum Mapping: Aligning course outcomes with program objectives and industry needs.',
					'Syllabus Review: Annual auditing of all {PROGRAM} syllabi to ensure modern office technologies are included.',
					'Stakeholder Consultation: Gathering feedback from alumni and partner industries.',
				],
			],
			[
				'title' => 'Faculty Competency and Development',
				'description' => 'To guarantee that {PROGRAM} instructors possess the necessary academic qualifications and industry certifications.',
				'type' => 'long term',
				'tasks' => [
					'Faculty Training Needs Analysis (TNA): Identifying gaps in faculty knowledge (e.g., new ERP software or virtual assistant tools).',
					'Credential Verification: Ensuring all faculty have the required Master\'s degrees or National Certificates (NC).',
					'Performance Evaluation: Regular classroom observations and student feedback loops.',
				],
			],
			[
				'title' => 'Enhancement of Laboratory and Learning Resources',
				'description' => 'To provide {PROGRAM} students with a physical or virtual environment that simulates a professional office setting.',
				'type' => 'long term',
				'tasks' => [
					'Inventory Management: Maintaining a log of functioning computers, typewriters (if applicable), and office equipment.',
					'Maintenance Scheduling: Routine updates for office software and hardware repairs.',
					'Safety Audit: Ensuring the laboratory complies with health and safety standards.',
				],
			],
			[
				'title' => 'Internship and Placement Monitoring',
				'description' => 'To manage the transition of {PROGRAM} students from the classroom to the professional workforce effectively.',
				'type' => 'long term',
				'tasks' => [
					'MOA Management: Establishing and renewing Memorandums of Agreement with reputable host training agencies.',
					'Internship Monitoring: Regular visits or check-ins with supervisors at the companies where {PROGRAM} students are interning.',
					'Traceability of Graduates: Tracking employment rates of {PROGRAM} alumni.',
				],
			],
			[
				'title' => 'Document Control and Records Management',
				'description' => 'To demonstrate "Good Housekeeping" by maintaining a systematic filing system for all {PROGRAM} departmental records.',
				'type' => 'short term',
				'tasks' => [
					'Master List of Documents: Creating a registry for all internal forms and procedures.',
					'Archive Management: Properly disposing of or archiving old student records according to data privacy laws.',
				],
			],
		];

		$now = now();

		foreach ($programs as $program) {
			$this->command->info("Seeding goals for program: {$program->name} (ID: {$program->id})");

			foreach ($goalsTemplate as $goalData) {
				// Replace {PROGRAM} placeholder with the actual program name
				$title = str_replace('{PROGRAM}', $program->name, $goalData['title']);
				$description = str_replace('{PROGRAM}', $program->name, $goalData['description']);

				$goal = Goal::create([
					'sdg_id' => $program->id,
					'project_manager_id' => $projectManager->id,
					'title' => $title,
					'slug' => Str::slug($title . '-' . $program->slug . '-' . Str::random(6)),
					'description' => $description,
					'start_date' => $now->copy()->addDays(rand(1, 10)),
					'end_date' => $now->copy()->addMonths(rand(6, 18)),
					'status' => 'pending',
					'type' => $goalData['type'],
					'compliance_percentage' => 0,
				]);

				// Attach to pivot too (in case both FKs and pivot are used)
				$goal->sdgs()->attach($program->id);

				// Create tasks
				foreach ($goalData['tasks'] as $taskTitle) {
					$taskTitle = str_replace('{PROGRAM}', $program->name, $taskTitle);

					Task::create([
						'goal_id' => $goal->id,
						'sdg_id' => $program->id,
						'title' => $taskTitle,
						'slug' => Str::slug($taskTitle . '-' . $program->slug . '-' . Str::random(6)),
						'description' => null,
						'status' => 'pending',
						'remarks' => null,
						'deadline' => $now->copy()->addMonths(rand(1, 6)),
					]);
				}
			}
		}

		$this->command->newLine();
		$this->command->info('✓ Goals and tasks seeded for ALL programs successfully!');
	}
}