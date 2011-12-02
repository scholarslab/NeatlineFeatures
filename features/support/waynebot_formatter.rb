require 'rubygems'

module Waynebot
  class CucumberFormatter

    def initialize(step_mother, io, options)
    end

    def step_name(keyword, step_match, status, source_indent, background)
      if status == :failed
        step_name = step_match.format_args(lambda{|param| "*#{param}*"})
        message = "#{step_name} FAILED"
        `say 'You have failed. Prepare to be destroyed'`
      end
    end
  end
end
